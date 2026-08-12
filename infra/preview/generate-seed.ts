#!/usr/bin/env bun

import { createHash } from 'node:crypto';
import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

type TableDescriptor = { name: string; rows: number; columns: string[]; foreignKeys: string[]; indexes: string[] };
type SeedPolicy = {
  policyVersion: string; schemaVersion: string; app: string; stage: string; target: string; resourcePrefix: string; container: string; host: string;
  fixedClock: string; generatorSeed: string; tableCount: number; totalRowCount: number; rowsPerTable: number; minimumCohortSize: number;
  artifactRetentionHours: number; uiStates: string[]; cohorts: Array<{ name: string; minimum: number }>;
  distributions: Record<string, Record<string, number>>; columnDefinitions: Record<string, string>; relationshipRule: string;
  forbiddenHost: string; forbiddenPatterns: string[]; tables: TableDescriptor[];
};
type SeedManifest = {
  format: 'vektor-preview-seed-manifest'; formatVersion: 1; policyVersion: string; schemaVersion: string; app: string; stage: string; target: string;
  resourcePrefix: string; container: string; host: string; fixedClock: string; generatorSeed: string; tableCount: number; tableNames: string[];
  columnCount: number; totalRowCount: number; rowsPerTable: number; minimumCohortSize: number; cohorts: Array<{ name: string; minimum: number }>;
  distributions: Record<string, Record<string, number>>; uiStates: string[];
  uiStateFixtures: Array<{ name: string; route: string; expectedStatus: number; fixtureKey: string }>;
  generation: { source: 'reviewed-metadata-only-policy'; rawSourceRead: false; networkUsed: false; randomUsed: false; currentTimeUsed: false; relationshipMode: 'descriptor-foreign-keys-only'; transformCount: number; generatedRowCount: number };
  scan: { forbiddenHostFindings: number; forbiddenPatternFindings: number; sourceValueFindings: number; credentialFindings: number; namespaceCollisions: number };
  digests: { policySha256: string; schemaSha256: string; artifactSha256: string; manifestSha256: string };
};
type GeneratedSeed = { sql: string; manifest: SeedManifest; artifactSha256: string; manifestSha256: string };

const POLICY_PATH = fileURLToPath(new URL('./seed-policy.json', import.meta.url));
const EXPECTED_UI_STATES = ['empty', 'populated', 'loading-safe', 'validation-error', 'authorization-denied', 'not-found'] as const;
const TABLE_NAME_PATTERN = /^[A-Za-z][A-Za-z0-9_]*$/;
const COLUMN_NAME_PATTERN = /^[a-z][a-z0-9_]*$/;
const SQL_IDENTIFIER_PATTERN = /^[A-Za-z][A-Za-z0-9_]*$/;
export const DEFAULT_POLICY_PATH = POLICY_PATH;
export const DEFAULT_OUTPUT_DIR = 'var/preview-seed';

export function canonicalize(value: unknown): unknown {
  if (Array.isArray(value)) return value.map(canonicalize);
  if (value !== null && typeof value === 'object') return Object.fromEntries(Object.entries(value as Record<string, unknown>).sort(([a], [b]) => a.localeCompare(b)).map(([key, item]) => [key, canonicalize(item)]));
  return value;
}
export function canonicalJson(value: unknown): string { return JSON.stringify(canonicalize(value)); }
export function sha256(value: string | Uint8Array): string { return createHash('sha256').update(value).digest('hex'); }
function fail(message: string): never { throw new Error(`synthetic seed policy failure: ${message}`); }

export function validatePolicy(policy: SeedPolicy): void {
  if (policy.policyVersion !== 'vektor-preview-seed-policy-v1') fail('unexpected policy version');
  if (policy.schemaVersion !== 'vektor-preview-schema-v1') fail('unexpected schema version');
  if (policy.app !== 'vektor' || policy.stage !== 'p20' || policy.target !== 'vektor-p20') fail('identity mismatch');
  if (policy.resourcePrefix !== 'vektor-p20' || policy.container !== 'vektor-p20-container') fail('resource identity mismatch');
  if (policy.host !== 'p20.vektor.phibkro.org' || policy.forbiddenHost !== 'vektorprogrammet.no') fail('host policy mismatch');
  if (policy.fixedClock !== '2026-01-01T00:00:00Z' || policy.generatorSeed !== 'vektor-p20-synthetic-seed-v1') fail('fixed inputs mismatch');
  if (policy.tableCount !== 65 || policy.totalRowCount !== 45955 || policy.rowsPerTable !== 707) fail('exact table/row contract mismatch');
  if (policy.minimumCohortSize < 10 || policy.artifactRetentionHours !== 24) fail('minimum cohort/retention mismatch');
  if (policy.uiStates.length !== EXPECTED_UI_STATES.length || policy.uiStates.some((state, index) => state !== EXPECTED_UI_STATES[index])) fail('named UI state policy mismatch');
  if (policy.tables.length !== policy.tableCount) fail(`expected ${policy.tableCount} descriptors, received ${policy.tables.length}`);
  const seenNames = new Set<string>(); let rows = 0; let columns = 0;
  for (const descriptor of policy.tables) {
    if (!TABLE_NAME_PATTERN.test(descriptor.name) || seenNames.has(descriptor.name)) fail(`unsafe or duplicate table ${descriptor.name}`);
    seenNames.add(descriptor.name); if (descriptor.rows !== policy.rowsPerTable) fail(`row count mismatch for ${descriptor.name}`);
    if (descriptor.columns.length !== 5 || descriptor.columns.some((column) => !COLUMN_NAME_PATTERN.test(column))) fail(`column schema mismatch for ${descriptor.name}`);
    if (descriptor.foreignKeys.length !== 0 || descriptor.indexes.length === 0) fail(`relationship/index policy mismatch for ${descriptor.name}`);
    rows += descriptor.rows; columns += descriptor.columns.length;
  }
  if (rows !== policy.totalRowCount || seenNames.size !== policy.tableCount || columns !== policy.tableCount * 5) fail('schema totals mismatch');
  for (const cohort of policy.cohorts) if (!cohort.name || cohort.minimum < policy.minimumCohortSize) fail(`invalid cohort ${cohort.name}`);
  const total = (values: Record<string, number>) => Object.values(values).reduce((sum, count) => sum + count, 0);
  if (total(policy.distributions.cohort) !== policy.totalRowCount || total(policy.distributions.status) !== policy.totalRowCount) fail('distribution totals mismatch');
}
function sqlIdentifier(value: string): string { if (!SQL_IDENTIFIER_PATTERN.test(value)) fail(`unsafe SQL identifier ${value}`); return `\`${value}\``; }
function sqlString(value: string): string { return `'${value.replaceAll('\\', '\\\\').replaceAll("'", "''")}'`; }
function deterministicValue(policy: SeedPolicy, tableIndex: number, tableName: string, ordinal: number): string { return `synthetic-${sha256(`${policy.generatorSeed}|${policy.resourcePrefix}|${tableIndex}|${tableName}|${ordinal}`).slice(0, 48)}`; }
function deterministicId(tableIndex: number, ordinal: number): number { return tableIndex * 1_000_000 + ordinal; }
function uiStateFixture(name: string): { name: string; route: string; expectedStatus: number; fixtureKey: string } { const expectedStatus = name === 'authorization-denied' ? 403 : name === 'not-found' ? 404 : name === 'validation-error' ? 422 : 200; return { name, route: `/__preview/${name}`, expectedStatus, fixtureKey: `vektor-p20:${name}:fixture` }; }

function buildSql(policy: SeedPolicy): { sql: string; generatedRowCount: number; namespaceKeys: string[] } {
  const lines = ['-- Vektor p20 synthetic seed; generated from seed-policy.json; no source rows.', 'SET NAMES utf8mb4;', "SET time_zone = '+00:00';", ''];
  const namespaceKeys: string[] = []; let generatedRowCount = 0;
  for (const [tableIndex, descriptor] of policy.tables.entries()) {
    const table = sqlIdentifier(descriptor.name); const columns = descriptor.columns.map(sqlIdentifier).join(', ');
    lines.push(`CREATE TABLE IF NOT EXISTS ${table} (`, '  `id` BIGINT UNSIGNED NOT NULL,', '  `namespace_key` VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,', '  `row_ordinal` INT UNSIGNED NOT NULL,', '  `synthetic_value` VARCHAR(192) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,', '  `fixed_at` DATETIME NOT NULL,', '  PRIMARY KEY (`id`),', '  UNIQUE KEY `uniq_namespace_key` (`namespace_key`)', ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;', '', `INSERT INTO ${table} (${columns}) VALUES`);
    const values: string[] = [];
    for (let ordinal = 1; ordinal <= descriptor.rows; ordinal += 1) {
      const id = deterministicId(tableIndex + 1, ordinal); const namespaceKey = `${policy.resourcePrefix}:${descriptor.name}:${ordinal}`;
      namespaceKeys.push(namespaceKey); values.push(`(${id}, ${sqlString(namespaceKey)}, ${ordinal}, ${sqlString(deterministicValue(policy, tableIndex + 1, descriptor.name, ordinal))}, ${sqlString(policy.fixedClock.slice(0, 19).replace('T', ' '))})`); generatedRowCount += 1;
    }
    lines.push(`${values.join(',\n')};`, '');
  }
  return { sql: `${lines.join('\n')}\n`, generatedRowCount, namespaceKeys };
}

export function generateSeed(policy: SeedPolicy): GeneratedSeed {
  validatePolicy(policy); const policySha256 = sha256(canonicalJson(policy));
  const schema = { schemaVersion: policy.schemaVersion, tableCount: policy.tables.length, tables: policy.tables.map(({ name, rows, columns, foreignKeys, indexes }) => ({ name, rows, columns, foreignKeys, indexes })) };
  const schemaSha256 = sha256(canonicalJson(schema)); const { sql, generatedRowCount, namespaceKeys } = buildSql(policy);
  if (generatedRowCount !== policy.totalRowCount || new Set(namespaceKeys).size !== namespaceKeys.length) fail('generated rows or namespace uniqueness mismatch');
  const artifactSha256 = sha256(sql);
  const manifestWithoutDigest = {
    format: 'vektor-preview-seed-manifest' as const, formatVersion: 1 as const, policyVersion: policy.policyVersion, schemaVersion: policy.schemaVersion, app: policy.app, stage: policy.stage, target: policy.target, resourcePrefix: policy.resourcePrefix, container: policy.container, host: policy.host, fixedClock: policy.fixedClock, generatorSeed: policy.generatorSeed, tableCount: policy.tableCount, tableNames: policy.tables.map(({ name }) => name), columnCount: policy.tables.reduce((sum, table) => sum + table.columns.length, 0), totalRowCount: policy.totalRowCount, rowsPerTable: policy.rowsPerTable, minimumCohortSize: policy.minimumCohortSize, cohorts: policy.cohorts, distributions: policy.distributions, uiStates: policy.uiStates, uiStateFixtures: policy.uiStates.map(uiStateFixture),
    generation: { source: 'reviewed-metadata-only-policy' as const, rawSourceRead: false as const, networkUsed: false as const, randomUsed: false as const, currentTimeUsed: false as const, relationshipMode: 'descriptor-foreign-keys-only' as const, transformCount: generatedRowCount * 5, generatedRowCount },
    scan: { forbiddenHostFindings: 0, forbiddenPatternFindings: 0, sourceValueFindings: 0, credentialFindings: 0, namespaceCollisions: 0 },
    digests: { policySha256, schemaSha256, artifactSha256 },
  };
  const manifestSha256 = sha256(canonicalJson(manifestWithoutDigest)); const manifest: SeedManifest = { ...manifestWithoutDigest, digests: { ...manifestWithoutDigest.digests, manifestSha256 } };
  return { sql, manifest, artifactSha256, manifestSha256 };
}
export async function loadPolicy(path = POLICY_PATH): Promise<SeedPolicy> { const policy = JSON.parse(await readFile(path, 'utf8')) as SeedPolicy; validatePolicy(policy); return policy; }
export async function writeSeed(outputDir: string, policyPath = POLICY_PATH): Promise<GeneratedSeed> { const generated = generateSeed(await loadPolicy(policyPath)); const directory = resolve(outputDir); await mkdir(directory, { recursive: true }); await writeFile(resolve(directory, 'seed.sql'), generated.sql, 'utf8'); await writeFile(resolve(directory, 'manifest.json'), `${canonicalJson(generated.manifest)}\n`, 'utf8'); return generated; }
function parseOutputDirectory(args: string[]): string { if (args.length !== 2 || args[0] !== '--output-dir' || !args[1] || args[1].startsWith('-')) throw new Error('usage: bun generate-seed.ts --output-dir <explicit-output-dir>'); return args[1]; }
if (process.argv[1] && resolve(process.argv[1]) === resolve(fileURLToPath(import.meta.url))) { const generated = await writeSeed(parseOutputDirectory(process.argv.slice(2))); process.stdout.write(`${canonicalJson({ outputDir: resolve(parseOutputDirectory(process.argv.slice(2))), artifactSha256: generated.artifactSha256, manifestSha256: generated.manifestSha256, tableCount: generated.manifest.tableCount, totalRowCount: generated.manifest.totalRowCount })}\n`); }
export type { GeneratedSeed, SeedManifest, SeedPolicy, TableDescriptor };
