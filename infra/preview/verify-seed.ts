#!/usr/bin/env bun
import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { generateSeed, loadPolicy, canonicalJson, sha256 } from './generate-seed';

const forbidden = [/vektorprogrammet\.no/i, /password/i, /passwd/i, /secret/i, /token/i, /credential/i, /raw[_ -]?backup/i, /source[_ -]?row/i, /https?:\/\//i, /@[A-Za-z]/];
const sqlString = /'(?:''|\\.|[^'])*'/gu;
function sqlContent(sql: string): string {
  return [...sql.matchAll(sqlString)]
    .map(([value]) => value.slice(1, -1).replaceAll("''", "'"))
    .filter((value) => !/^vektor-p20:[A-Za-z][A-Za-z0-9_]*:[0-9]+$/u.test(value))
    .join("\n");
}
function fail(message: string): never { throw new Error(`synthetic seed verification failed: ${message}`); }
function arg(name: string, args: string[]): string { const index = args.indexOf(name); const value = index >= 0 ? args[index + 1] : undefined; if (!value || value.startsWith('-')) fail(`missing ${name}`); return value; }

export async function verifySeed(outputDir: string, policyPath?: string): Promise<Record<string, unknown>> {
  const policy = await loadPolicy(policyPath);
  const expected = generateSeed(policy);
  const sql = await readFile(resolve(outputDir, 'seed.sql'), 'utf8');
  const manifest = JSON.parse(await readFile(resolve(outputDir, 'manifest.json'), 'utf8')) as typeof expected.manifest;
  if (sql !== expected.sql) fail('artifact does not match deterministic replay');
  if (manifest.digests.artifactSha256 !== sha256(sql)) fail('artifact digest mismatch');
  const withoutManifestDigest = { ...manifest, digests: { ...manifest.digests } };
  delete withoutManifestDigest.digests.manifestSha256;
  if (manifest.digests.manifestSha256 !== sha256(canonicalJson(withoutManifestDigest))) fail('manifest digest mismatch');
  if (manifest.tableCount !== 65 || manifest.totalRowCount !== 45955 || manifest.tableNames.length !== 65) fail('exact table/row contract mismatch');
  if (manifest.generation.generatedRowCount !== 45955 || manifest.generation.rawSourceRead || manifest.generation.networkUsed || manifest.generation.randomUsed || manifest.generation.currentTimeUsed) fail('generation safety flags mismatch');
  for (const pattern of forbidden) if (pattern.test(sqlContent(sql))) fail(`forbidden artifact pattern ${pattern}`);
  const second = generateSeed(policy);
  if (second.artifactSha256 !== manifest.digests.artifactSha256 || second.manifestSha256 !== manifest.digests.manifestSha256) fail('replay digest mismatch');
  return { outputDir: resolve(outputDir), tableCount: manifest.tableCount, totalRowCount: manifest.totalRowCount, artifactSha256: manifest.digests.artifactSha256, manifestSha256: manifest.digests.manifestSha256, forbiddenFindings: 0, deterministicReplay: true };
}

if (process.argv[1] && resolve(process.argv[1]) === resolve(new URL(import.meta.url).pathname)) {
  const outputDir = arg('--output-dir', process.argv.slice(2));
  process.stdout.write(`${canonicalJson(await verifySeed(outputDir))}\n`);
}
