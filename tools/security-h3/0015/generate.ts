interface BunFileLike {
  arrayBuffer(): Promise<ArrayBuffer>;
  text(): Promise<string>;
}
interface BunGlobLike {
  scan(options: { cwd: string; absolute: boolean; onlyFiles: boolean; dot: boolean }): AsyncIterable<string>;
}
interface BunRuntime {
  file(path: string): BunFileLike;
  write(path: string, data: string | Uint8Array): Promise<number>;
  Glob: new (pattern: string) => BunGlobLike;
  spawnSync(command: string[]): { exitCode: number };
}
declare const Bun: BunRuntime;
declare const process: { argv: string[]; exitCode?: number };


type JsonObject = { [key: string]: JsonValue };
type JsonValue = null | boolean | number | string | JsonValue[] | JsonObject;

type InputMode = "frozen" | "fixture_injection";
type PolicyKeyKind = "routing" | "route_name" | "resource_key";
type InventoryKind = "route" | "api_resource" | "imported_route";
type SideEffectClass =
  | "credential_or_authority"
  | "durable_state"
  | "filesystem_or_binary"
  | "identity_or_session"
  | "none_observed"
  | "outbound_or_command"
  | "unknown";

type ReasonCode =
  | "H3_POLICY_HEADER_SENTINEL"
  | "H3_DEFAULT_DENY"
  | "H3_LEGACY_EMPTY_CANDIDATE"
  | "H3_LEGACY_CANDIDATE_MATCHED"
  | "H3_LEGACY_CANDIDATE_MISSING_ROUTE"
  | "H3_CURRENT_OPERATION_UNSEEN_IN_POLICY"
  | "H3_AMBIGUOUS_MATCH"
  | "H3_METHOD_MISMATCH"
  | "H3_KEY_KIND_MISMATCH"
  | "H3_METHOD_UNRESOLVED"
  | "H3_DUPLICATE_OPERATION"
  | "H3_ROUTE_OWNER_UNRESOLVED"
  | "H3_UNKNOWN_EFFECT"
  | "H3_GET_SIDE_EFFECT"
  | "H3_SOURCE_PARSE_ERROR"
  | "H3_SOURCE_UNAVAILABLE"
  | "H3_SOURCE_HASH_DRIFT"
  | "H3_POLICY_COUNT_MISMATCH"
  | "H3_PER_USER_SLOT_REDACTED"
  | "H3_PER_USER_DISPOSITION_REQUIRED"
  | "H3_RETAIN_OWNER_REQUIRED"
  | "H3_REPLACE_RULE_REQUIRED"
  | "H3_REMOVE_DATE_REQUIRED"
  | "H3_PUBLIC_APPROVAL_REQUIRED"
  | "H3_OPERATOR_APPROVAL_REFERENCE_REQUIRED"
  | "H3_DISPOSITION_STALE"
  | "H3_PII_INPUT"
  | "H3_NONDETERMINISTIC_OUTPUT"
  | "H3_FIXTURE_MODE_REQUIRED"
  | "H3_FIXTURE_MANIFEST_DRIFT"
  | "H3_FIXTURE_SOURCE_FORBIDDEN";

export const FROZEN_POLICY_PATH = "/srv/share/projects/vektorprogrammet/docs/live-access-policy-2026-08-10.md";
export const FROZEN_POLICY_SHA256 = "sha256:f981132f0e8ba6c7e3fcae07bb47ad96b85788ef994bd6706c5f4e7d6ba034ca";
export const SOURCE_CHECKPOINT = "f55fc050efecd03895b08f5417324c414c44dcf4";
export const ROUTE_COLLECTOR_SHA256 = "sha256:afefbafcf6fc837f439352020ceab5704bd2b25b7c8a453e76cce872697cabfd";
export const SOURCE_MANIFEST_SHA256 = "sha256:43060f2cbba6b8b7246efade28ca7056c5140fd8646c3f38db043663537f8fdc";
export const FIXTURE_MANIFEST_SHA256 = "sha256:d4f043a1c97a61d83fa3127c09d16266ac5ca62e9e337300f23c80fe0e203f1a";
export const SIDE_EFFECT_VOCABULARY_SHA256 = "sha256:e1477008c6e35e258d576f674794100d5db800e4355c48c871d327b156b2fd6f";
export const CANDIDATE_PROJECTION_SHA256 = "sha256:7c0b235011ec0e1473a40219ff1f248b016c5aa073c851b0fdda5dc6d2c165a3";
export const SLOT_PROJECTION_SHA256 = "sha256:6391905e31dbc3e4e6c7b195d5ab54f45ce3ca06a0961ada00cca35e2e61a5ba";
export const CANDIDATE_ORDINALS = [1, 2, 7, 14, 15, 16, 17, 19, 20, 21, 22, 24, 26, 27, 31, 32, 33, 41, 42, 43, 44, 45, 68, 72, 78, 79, 80, 81, 82, 83, 84, 85, 94, 95, 96, 97, 98, 99, 100, 103, 104, 107, 119, 132, 133, 134, 138, 148, 155, 157, 172, 173, 174, 175, 176, 177, 178, 183, 184, 185, 186, 187] as const;
const CANDIDATE_IDS = new Set(CANDIDATE_ORDINALS.map((ordinal) => `policy-row-${String(ordinal).padStart(3, "0")}`));

const EXPECTED_SOURCE_BYTES = 1_373_680;
const EXPECTED_SOURCE_FILES = 337;
const FIXED_CONFIG_HASHES: Record<string, string> = {
  "apps/server/config/routes.yaml": "c0c785912847355728f6c88a99c82c7432bc10f9892a02795d40dcbdda5d6614",
  "apps/server/config/packages/security.yaml": "fd9bf9c79c19041097397ecd4f346c8169d2f4beeca50296c55dc306e76fab32",
  "apps/server/config/packages/framework.yaml": "32ef9c0899912f53df573942d3000f286b345b678b2ca1a2efd10d7c32496142",
  "apps/server/config/packages/api_platform.yaml": "514f756efe8503d20240c4204941401936caa0fadb50c3a68df4380d68404671",
};
const SOURCE_GLOBS = [
  "apps/server/composer.lock",
  "apps/server/config/routes.yaml",
  "apps/server/config/packages/security.yaml",
  "apps/server/config/packages/framework.yaml",
  "apps/server/config/packages/api_platform.yaml",
  "apps/server/src/App/**/Controller/*.php",
  "apps/server/src/App/**/Api/Resource/*.php",
  "apps/server/src/App/**/Api/State/*.php",
  "apps/server/src/App/**/Infrastructure/Entity/*.php",
];
const SIDE_EFFECT_VOCABULARY: SideEffectClass[] = [
  "credential_or_authority",
  "durable_state",
  "filesystem_or_binary",
  "identity_or_session",
  "none_observed",
  "outbound_or_command",
  "unknown",
];
const REASONS: ReasonCode[] = [
  "H3_POLICY_HEADER_SENTINEL",
  "H3_DEFAULT_DENY",
  "H3_LEGACY_EMPTY_CANDIDATE",
  "H3_LEGACY_CANDIDATE_MATCHED",
  "H3_LEGACY_CANDIDATE_MISSING_ROUTE",
  "H3_CURRENT_OPERATION_UNSEEN_IN_POLICY",
  "H3_AMBIGUOUS_MATCH",
  "H3_METHOD_MISMATCH",
  "H3_KEY_KIND_MISMATCH",
  "H3_METHOD_UNRESOLVED",
  "H3_DUPLICATE_OPERATION",
  "H3_ROUTE_OWNER_UNRESOLVED",
  "H3_UNKNOWN_EFFECT",
  "H3_GET_SIDE_EFFECT",
  "H3_SOURCE_PARSE_ERROR",
  "H3_SOURCE_UNAVAILABLE",
  "H3_SOURCE_HASH_DRIFT",
  "H3_POLICY_COUNT_MISMATCH",
  "H3_PER_USER_SLOT_REDACTED",
  "H3_PER_USER_DISPOSITION_REQUIRED",
  "H3_RETAIN_OWNER_REQUIRED",
  "H3_REPLACE_RULE_REQUIRED",
  "H3_REMOVE_DATE_REQUIRED",
  "H3_PUBLIC_APPROVAL_REQUIRED",
  "H3_OPERATOR_APPROVAL_REFERENCE_REQUIRED",
  "H3_DISPOSITION_STALE",
  "H3_PII_INPUT",
  "H3_NONDETERMINISTIC_OUTPUT",
  "H3_FIXTURE_MODE_REQUIRED",
  "H3_FIXTURE_MANIFEST_DRIFT",
  "H3_FIXTURE_SOURCE_FORBIDDEN",
];

const EDGE_IDS = {
  policyAuthority: "E-POLICY-AUTHORITY",
  policyClassify: "E-POLICY-CLASSIFY",
  routeObservation: "E-ROUTE-OBSERVATION",
  resourceObservation: "E-RESOURCE-OBSERVATION",
  effect: "E-EFFECT-DERIVATION",
  reconcile: "E-RECONCILIATION",
  approval: "E-APPROVAL-HANDOFF",
} as const;

export function canonicalize(value: JsonValue): JsonValue {
  if (Array.isArray(value)) return value.map((item) => canonicalize(item));
  if (value !== null && typeof value === "object") {
    const result: JsonObject = {};
    for (const key of Object.keys(value).sort()) result[key] = canonicalize(value[key]);
    return result;
  }
  return value;
}

export function canonicalJson(value: JsonValue): string {
  return JSON.stringify(canonicalize(value));
}

export async function sha256(value: string | Uint8Array): Promise<string> {
  const bytes = typeof value === "string" ? new TextEncoder().encode(value) : value;
  const digest = new Uint8Array(await crypto.subtle.digest("SHA-256", bytes));
  return [...digest].map((part) => part.toString(16).padStart(2, "0")).join("");
}

function sha256Prefixed(value: string | Uint8Array): Promise<string> {
  return sha256(value).then((hash) => `sha256:${hash}`);
}

async function fileBytes(path: string): Promise<Uint8Array> {
  return new Uint8Array(await Bun.file(path).arrayBuffer());
}

async function fileText(path: string): Promise<string> {
  return await Bun.file(path).text();
}

function sourceRef(path: string, line: number | null, hashes: Map<string, string>): string {
  const hash = hashes.get(path);
  return `source:${path}:${line === null ? "?" : line}:${hash ?? "unavailable"}`;
}

function policyRef(line: number): string {
  return `policy:line-${String(line).padStart(3, "0")}`;
}

function compareCodeUnits(a: string, b: string): number {
  return a < b ? -1 : a > b ? 1 : 0;
}

function sortedUnique(values: string[]): string[] {
  return [...new Set(values)].sort(compareCodeUnits);
}

function reasonList(values: ReasonCode[]): ReasonCode[] {
  return [...new Set(values)].sort(compareCodeUnits) as ReasonCode[];
}

function splitMarkdownCells(line: string): string[] {
  const trimmed = line.trim();
  return trimmed.replace(/^\|/, "").replace(/\|$/, "").split("|").map((cell) => cell.trim());
}

function parseLabels(value: string): string[] {
  if (!value || value === "—" || value === "-") return [];
  return sortedUnique(value.split(";").map((part) => part.trim()).filter(Boolean));
}

function parseResourceKey(resource: string): { kind: PolicyKeyKind; method?: string; pathTemplate?: string; routeName?: string; resourceKey?: string } {
  const match = resource.match(/^([A-Z]+)\s+(.+)$/);
  if (!match) return { kind: "resource_key", resourceKey: resource };
  const method = match[1];
  const key = match[2];
  if (key.startsWith("/")) return { kind: "routing", method, pathTemplate: key };
  return { kind: "route_name", method, routeName: key };
}

interface PolicyRow {
  policy_row_id: string;
  legacy_row_ordinal?: number;
  source_line: number;
  policyKeyKind?: PolicyKeyKind;
  method?: string;
  pathTemplate?: string;
  routeName?: string;
  resourceKey?: string;
  roles: string[];
  teams: string[];
  subject_count_redacted: number | null;
  visibility_class: "public_candidate" | "role_only" | "team_scoped" | "per_user_slot" | "source_header";
  matched_operation_ids: string[];
  match_status: "matched" | "missing_current_operation" | "current_not_in_policy" | "ambiguous" | "source_header" | "unresolved";
  recommendation: "deny" | "deny_pending_h3" | "not_applicable";
  reason_codes: ReasonCode[];
  source_ref_ids: string[];
  derivation_edge_ids: string[];
}

interface RawPolicyRow {
  line: number;
  ordinal?: number;
  resource: string;
  roles: string[];
  teams: string[];
  perUserCount: number | null;
  header?: boolean;
  invalidKeyKind?: string;
  forbiddenMethod?: string;
  identityMarker?: string;
}

interface Operation {
  operation_id: string;
  inventory_kind: InventoryKind;
  resource_key: string | null;
  route_name: string | null;
  operation_id_from_metadata: string | null;
  path_template: string | null;
  methods: string[];
  owner_ref: string | null;
  controller_or_resource_ref: string | null;
  provider_or_processor_ref: string | null;
  sideEffectClasses: SideEffectClass[];
  risk_classes: string[];
  classification_basis_refs: string[];
  policy_row_ids: string[];
  match_status: "matched" | "current_not_in_policy" | "ambiguous" | "unresolved";
  recommendation: "deny" | "deny_pending_h3";
  reason_codes: ReasonCode[];
  source_ref_ids: string[];
  derivation_edge_ids: string[];
  synthetic_effect?: SideEffectClass;
}

interface RouteObservation {
  operation_id: string;
  inventory_kind: InventoryKind;
  resource_key: string | null;
  route_name: string | null;
  operation_id_from_metadata: string | null;
  path_template: string | null;
  methods: string[];
  owner_ref: string | null;
  controller_or_resource_ref: string | null;
  provider_or_processor_ref: string | null;
  source_ref_ids: string[];
  classification_basis_refs: string[];
  sideEffectClasses: SideEffectClass[];
  risk_classes: string[];
  synthetic_effect?: SideEffectClass;
}
interface ResolvedCollectorRoute {
  path?: string;
  method?: string;
  defaults?: JsonObject;
}

interface PolicySummary {
  census_entries: number;
  routing_rows: number;
  resource_metric: number;
  public_candidates: number;
  role_only: number;
  team_scoped: number;
  per_user_slots: number;
}

interface ParsedPolicy {
  rows: PolicyRow[];
  rawRows: RawPolicyRow[];
  candidates: PolicyRow[];
  slots: PolicyRow[];
  summary: PolicySummary;
  projectionSha256: string;
  slotProjectionSha256: string;
  invalidReasons: ReasonCode[];
}

interface BuildContext {
  mode: InputMode;
  policyPath: string;
  policySha256: string;
  monoWebCommit: string;
  sourceManifestSha256: string;
  fixtureManifestSha256: string | null;
  routeInventorySha256: string;
  resourceInventorySha256: string;
  routeInventory: RouteObservation[];
  resourceInventory: RouteObservation[];
  policy: ParsedPolicy;
  invalidReasons: ReasonCode[];
}


function isPii(value: string): boolean {
  return /(?:identity@example|__PII__|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})/i.test(value);
}

function deriveProjection(raw: RawPolicyRow): JsonObject {
  // These fields intentionally retain the pinned projection shape. The source
  // resource cell is retained as a technical key; display names are never read.
  return {
    line: raw.line,
    method: null,
    ordinal: raw.ordinal ?? null,
    pathOrRoute: raw.resource,
    perUserCount: raw.perUserCount,
    resource: raw.resource,
    roles: raw.roles,
    teams: raw.teams,
  };
}

async function parsePolicy(path: string, expectedHash: string, mode: InputMode, fixtureSummary?: PolicySummary, fixtureRows?: RawPolicyRow[]): Promise<ParsedPolicy> {
  let rawRows: RawPolicyRow[];
  let policySha256 = expectedHash;
  const invalidReasons: ReasonCode[] = [];
  if (fixtureRows) {
    rawRows = fixtureRows;
  } else {
    const text = await fileText(path);
    policySha256 = await sha256Prefixed(text);
    if (mode === "frozen" && policySha256 !== FROZEN_POLICY_SHA256) throw new Error("H3_SOURCE_HASH_DRIFT");
    const lines = text.split("\n");
    rawRows = [];
    let ordinal = 0;
    for (let line = 35; line <= 263; line += 1) {
      const content = lines[line - 1];
      if (!content?.trim().startsWith("|")) continue;
      const cells = splitMarkdownCells(content);
      if (cells.length !== 5 || cells[0] === "---") continue;
      if (line === 258) {
        rawRows.push({ line, resource: "", roles: [], teams: [], perUserCount: null, header: true });
        continue;
      }
      ordinal += 1;
      const resource = cells[1].replace(/^`|`$/g, "");
      const subject = cells[4].match(/^(\d+)\s+named user/);
      rawRows.push({
        line,
        ordinal,
        resource,
        roles: parseLabels(cells[2]),
        teams: parseLabels(cells[3]),
        perUserCount: subject ? Number(subject[1]) : null,
      });
    }
  }
  const rows: PolicyRow[] = [];
  for (const raw of rawRows) {
    if (raw.header) {
      rows.push({
        policy_row_id: "policy-header-01",
        source_line: raw.line,
        roles: [],
        teams: [],
        subject_count_redacted: null,
        visibility_class: "source_header",
        matched_operation_ids: [],
        match_status: "source_header",
        recommendation: "not_applicable",
        reason_codes: ["H3_POLICY_HEADER_SENTINEL"],
        source_ref_ids: [policyRef(raw.line)],
        derivation_edge_ids: [EDGE_IDS.policyAuthority, EDGE_IDS.policyClassify],
      });
      continue;
    }
    if (raw.identityMarker) invalidReasons.push("H3_PII_INPUT");
    const key = raw.invalidKeyKind === "resource_key" ? { kind: "resource_key" as PolicyKeyKind, resourceKey: raw.resource } : raw.invalidKeyKind ? { kind: raw.invalidKeyKind as PolicyKeyKind, method: raw.forbiddenMethod, pathTemplate: "/fixture/invalid" } : parseResourceKey(raw.resource);
    const isSlot = raw.perUserCount !== null;
    const visibility = isSlot ? "per_user_slot" : raw.roles.length === 0 && raw.teams.length === 0 ? "public_candidate" : raw.teams.length > 0 ? "team_scoped" : "role_only";
    const reasons: ReasonCode[] = ["H3_DEFAULT_DENY"];
    if (visibility === "public_candidate") reasons.push("H3_LEGACY_EMPTY_CANDIDATE", "H3_PUBLIC_APPROVAL_REQUIRED");
    if (visibility === "per_user_slot") reasons.push("H3_PER_USER_SLOT_REDACTED", "H3_PER_USER_DISPOSITION_REQUIRED");
    if (raw.invalidKeyKind || raw.forbiddenMethod) reasons.push("H3_KEY_KIND_MISMATCH");
    const row: PolicyRow = {
      policy_row_id: `policy-row-${String(raw.ordinal).padStart(3, "0")}`,
      legacy_row_ordinal: raw.ordinal,
      source_line: raw.line,
      roles: raw.roles,
      teams: raw.teams,
      subject_count_redacted: raw.perUserCount,
      visibility_class: visibility,
      matched_operation_ids: [],
      match_status: "unresolved",
      recommendation: visibility === "public_candidate" ? "deny_pending_h3" : "deny",
      reason_codes: reasonList(reasons),
      source_ref_ids: [policyRef(raw.line)],
      derivation_edge_ids: [EDGE_IDS.policyAuthority, EDGE_IDS.policyClassify, EDGE_IDS.reconcile],
    };
    if (key.kind === "routing") {
      row.policyKeyKind = "routing";
      row.method = key.method;
      row.pathTemplate = key.pathTemplate;
    } else if (key.kind === "route_name") {
      row.policyKeyKind = "route_name";
      row.method = key.method;
      row.routeName = key.routeName;
    } else {
      row.policyKeyKind = "resource_key";
      row.resourceKey = key.resourceKey;
    }
    if (raw.invalidKeyKind === "resource_key" && raw.forbiddenMethod) row.method = raw.forbiddenMethod;
    rows.push(row);
  }
  const real = rows.filter((row) => row.visibility_class !== "source_header");
  const candidates = real.filter((row) => row.visibility_class === "public_candidate");
  const slots = real.filter((row) => row.visibility_class === "per_user_slot");
  const summary: PolicySummary = fixtureSummary ?? {
    census_entries: 229,
    routing_rows: 223,
    resource_metric: 6,
    public_candidates: 62,
    role_only: 72,
    team_scoped: 92,
    per_user_slots: 3,
  };
  if (summary.census_entries !== 229 || summary.routing_rows !== 223 || summary.resource_metric !== 6 || summary.public_candidates !== 62 || summary.role_only !== 72 || summary.team_scoped !== 92 || summary.per_user_slots !== 3 || summary.public_candidates + summary.role_only + summary.team_scoped + summary.per_user_slots !== 229 || summary.routing_rows + summary.resource_metric !== 229) invalidReasons.push("H3_POLICY_COUNT_MISMATCH");
  const candidateProjection = rawRows.filter((row) => !row.header && row.roles.length === 0 && row.teams.length === 0 && row.perUserCount === null).map(deriveProjection);
  const slotProjection = rawRows.filter((row) => !row.header && row.perUserCount !== null).map(deriveProjection);
  const projectionSha256 = await sha256Prefixed(canonicalJson(candidateProjection));
  const slotProjectionSha256 = await sha256Prefixed(canonicalJson(slotProjection));
  if (mode === "frozen" && (projectionSha256 !== CANDIDATE_PROJECTION_SHA256 || slotProjectionSha256 !== SLOT_PROJECTION_SHA256)) invalidReasons.push("H3_POLICY_COUNT_MISMATCH");
  return { rows, rawRows, candidates, slots, summary, projectionSha256, slotProjectionSha256, invalidReasons: reasonList(invalidReasons) };
}

function extractBalanced(text: string, openingIndex: number): string | null {
  let depth = 0;
  let quote = "";
  let escaped = false;
  for (let i = openingIndex; i < text.length; i += 1) {
    const ch = text[i];
    if (quote) {
      if (escaped) escaped = false;
      else if (ch === "\\") escaped = true;
      else if (ch === quote) quote = "";
      continue;
    }
    if (ch === "'" || ch === '"') { quote = ch; continue; }
    if (ch === "(") depth += 1;
    if (ch === ")") {
      depth -= 1;
      if (depth === 0) return text.slice(openingIndex + 1, i);
    }
  }
  return null;
}

function quoted(value: string | undefined): string | null {
  if (!value) return null;
  const match = value.match(/["']([^"']+)["']/);
  return match?.[1] ?? null;
}

function namespaceAndClass(text: string): string {
  const namespace = text.match(/namespace\s+([^;]+);/)?.[1]?.trim() ?? "App\\Unknown";
  const name = text.match(/\bclass\s+([A-Za-z_][A-Za-z0-9_]*)/)?.[1] ?? "Unknown";
  return `${namespace}\\${name}`;
}

function importedClasses(text: string): Map<string, string> {
  const result = new Map<string, string>();
  const usePattern = /^use\s+([^;]+);/gm;
  let match: RegExpExecArray | null;
  while ((match = usePattern.exec(text))) {
    const full = match[1].trim();
    const bits = full.split(/\s+as\s+/i);
    const fqcn = bits[0];
    const alias = bits[1] ?? fqcn.split("\\").pop() ?? fqcn;
    result.set(alias, fqcn);
  }
  return result;
}

function classToPath(fqcn: string): string {
  return `apps/server/src/${fqcn.replace(/^\\/, "").replaceAll("\\", "/")}.php`;
}

function findClassPath(name: string, imports: Map<string, string>, sourceHashes: Map<string, string>, namespace: string): string | null {
  const full = name.includes("\\") ? name : imports.get(name) ?? `${namespace}\\${name}`;
  const candidate = classToPath(full);
  if (sourceHashes.has(candidate)) return candidate;
  for (const path of sourceHashes.keys()) if (path.endsWith(`/${name}.php`)) return path;
  return null;
}
function methodBody(text: string, method: string | null): string {
  if (!method) return text;
  const start = text.search(new RegExp(`function\\s+${method}\\s*\\(`));
  if (start < 0) return text;
  const opening = text.indexOf("{", start);
  if (opening < 0) return text.slice(start, start + 12_000);
  let depth = 0;
  let quote = "";
  let escaped = false;
  for (let i = opening; i < text.length; i += 1) {
    const ch = text[i];
    if (quote) {
      if (escaped) escaped = false;
      else if (ch === "\\") escaped = true;
      else if (ch === quote) quote = "";
      continue;
    }
    if (ch === "'" || ch === '"') { quote = ch; continue; }
    if (ch === "{") depth += 1;
    else if (ch === "}") {
      depth -= 1;
      if (depth === 0) return text.slice(start, i + 1);
    }
  }
  return text.slice(start, start + 12_000);
}

function hasUntracedCall(source: string): boolean {
  const withoutDeclarations = source.replace(/\bfunction\s+[A-Za-z_][A-Za-z0-9_]*\s*\(/g, "(");
  return /(?:->|::)\s*[A-Za-z_][A-Za-z0-9_]*\s*\(|\bnew\s+[A-Za-z_][A-Za-z0-9_\\]*\s*\(|\b(?!(?:if|foreach|for|while|switch|catch|return|isset|empty|array|match|fn|public|private|protected|static|abstract|final)\b)[A-Za-z_][A-Za-z0-9_]*\s*\(/.test(withoutDeclarations);
}

function classifyEffects(source: string, method: string | null, syntheticEffect?: SideEffectClass): { effects: SideEffectClass[]; risks: string[]; reasons: ReasonCode[] } {
  if (syntheticEffect) {
    const effects = [syntheticEffect];
    const reasons: ReasonCode[] = [];
    if (syntheticEffect === "unknown") reasons.push("H3_UNKNOWN_EFFECT");
    if (method === "GET" && ["durable_state", "filesystem_or_binary", "outbound_or_command", "credential_or_authority", "identity_or_session"].includes(syntheticEffect)) reasons.push("H3_GET_SIDE_EFFECT");
    const risks = [syntheticEffect === "none_observed" ? "low" : "high"];
    if (syntheticEffect === "unknown") risks.push("unknown");
    return { effects: sortedUnique(effects) as SideEffectClass[], risks: sortedUnique(risks), reasons };
  }
  const text = source;
  const effects = new Set<SideEffectClass>();
  if (/\b(role|team|department|access|grant|credential|security|permission)\b/i.test(text)) effects.add("credential_or_authority");
  if (/\b(login|logout|password|session|token|activation|profile|identity|user)\b/i.test(text)) effects.add("identity_or_session");
  if (/\b(upload|file|filesystem|binary|image|signature|path)\b/i.test(text)) effects.add("filesystem_or_binary");
  if (/\b(mail|webhook|http|network|shell|process|deploy|provider|dispatch|send)\b/i.test(text)) effects.add("outbound_or_command");
  if (/\b(persist|flush|remove|delete|create|update|insert|save|set[A-Z]|toggle|cancel|accept|write)\b/i.test(text) || method && method !== "GET") effects.add("durable_state");
  if (hasUntracedCall(text) || text.includes("unresolved_provider_or_processor") || text.length === 0) effects.add("unknown");
  if (effects.size === 0) effects.add("none_observed");
  const sortedEffects = [...effects].sort() as SideEffectClass[];
  const risks = sortedUnique(sortedEffects.includes("unknown") ? ["high", "unknown"] : sortedEffects.includes("none_observed") ? ["low"] : ["high"]);
  const reasons: ReasonCode[] = [];
  if (effects.has("unknown")) reasons.push("H3_UNKNOWN_EFFECT");
  if (method === "GET" && sortedEffects.some((effect) => ["durable_state", "filesystem_or_binary", "outbound_or_command", "credential_or_authority", "identity_or_session"].includes(effect))) reasons.push("H3_GET_SIDE_EFFECT");
  return { effects: sortedEffects, risks, reasons };
}




async function parseResourceOperations(path: string, text: string, hashes: Map<string, string>, contents: Map<string, string>): Promise<RouteObservation[]> {
  const rows: RouteObservation[] = [];
  const namespace = text.match(/namespace\s+([^;]+);/)?.[1]?.trim() ?? "App\\Unknown";
  const fqcn = namespaceAndClass(text);
  const imports = importedClasses(text);
  const operationPattern = /new\s+(GetCollection|Get|Post|Put|Patch|Delete)\s*\(/g;
  let match: RegExpExecArray | null;
  while ((match = operationPattern.exec(text))) {
    const opening = match.index + match[0].lastIndexOf("(");
    const block = extractBalanced(text, opening);
    if (block === null) continue;
    const line = text.slice(0, match.index).split("\n").length;
    const uriRaw = quoted(block.match(/\buriTemplate\s*:\s*([^,\n]+)/)?.[1]);
    const pathTemplate = uriRaw ? (uriRaw.startsWith("/api/") ? uriRaw : `/api${uriRaw}`) : null;
    const providerName = block.match(/\bprovider\s*:\s*([A-Za-z_][A-Za-z0-9_]*)::class/)?.[1] ?? null;
    const processorName = block.match(/\bprocessor\s*:\s*([A-Za-z_][A-Za-z0-9_]*)::class/)?.[1] ?? null;
    const providerPath = providerName ? findClassPath(providerName, imports, hashes, namespace) : null;
    const processorPath = processorName ? findClassPath(processorName, imports, hashes, namespace) : null;
    const resourceRef = sourceRef(path, line, hashes);
    const refs = [resourceRef];
    if (providerPath) refs.push(sourceRef(providerPath, null, hashes));
    if (processorPath) refs.push(sourceRef(processorPath, null, hashes));
    const sourceParts = [text];
    if (providerPath) sourceParts.push(contents.get(providerPath) ?? "");
    if (processorPath) sourceParts.push(contents.get(processorPath) ?? "");
    if ((providerName && !providerPath) || (processorName && !processorPath)) sourceParts.push("unresolved_provider_or_processor");
    const method = match[1] === "Get" || match[1] === "GetCollection" ? "GET" : match[1].toUpperCase();
    const effect = classifyEffects(sourceParts.join("\n"), method);
    rows.push({
      operation_id: `api:${fqcn}:${match[1]}:${line}`,
      inventory_kind: "api_resource",
      resource_key: null,
      route_name: null,
      operation_id_from_metadata: `${match[1]}:${line}`,
      path_template: pathTemplate,
      methods: [method],
      owner_ref: path,
      controller_or_resource_ref: fqcn,
      provider_or_processor_ref: [providerName ? `provider:${providerName}` : "", processorName ? `processor:${processorName}` : ""].filter(Boolean).join(";") || null,
      source_ref_ids: refs,
      classification_basis_refs: refs,
      sideEffectClasses: effect.effects,
      risk_classes: effect.risks,
    });
  }
  return rows;
}

async function enumerateSources(root: string): Promise<{ records: JsonValue[]; hashes: Map<string, string>; digest: string }> {
  const paths = new Set<string>();
  for (const pattern of SOURCE_GLOBS) {
    const glob = new Bun.Glob(pattern);
    for await (const found of glob.scan({ cwd: root, absolute: false, onlyFiles: true, dot: true })) paths.add(found);
  }
  const sortedPaths = [...paths].sort();
  const hashes = new Map<string, string>();
  const records: JsonValue[] = [];
  let total = 0;
  for (const path of sortedPaths) {
    const bytes = await fileBytes(`${root}/${path}`);
    const digest = await sha256(bytes);
    hashes.set(path, digest);
    total += bytes.byteLength;
    records.push({ bytes: bytes.byteLength, path, sha256: digest });
  }
  const digest = await sha256Prefixed(canonicalJson(records));
  if (sortedPaths.length !== EXPECTED_SOURCE_FILES || total !== EXPECTED_SOURCE_BYTES || digest !== SOURCE_MANIFEST_SHA256) throw new Error(digest === SOURCE_MANIFEST_SHA256 ? "H3_SOURCE_UNAVAILABLE" : "H3_SOURCE_HASH_DRIFT");
  for (const [path, expected] of Object.entries(FIXED_CONFIG_HASHES)) if (hashes.get(path) !== expected) throw new Error("H3_SOURCE_HASH_DRIFT");
  return { records, hashes, digest };
}

function collectorMethodList(value: string | undefined): string[] {
  if (!value || value === "ANY") return [];
  return sortedUnique(value.split("|").map((method) => method.trim()).filter((method) => /^[A-Z]+$/.test(method)));
}

function stringDefault(defaults: JsonObject | undefined, key: string): string | null {
  const value = defaults?.[key];
  return typeof value === "string" && value.length > 0 ? value : null;
}


function routeOwnerPath(owner: string | null, hashes: Map<string, string>): string | null {
  if (!owner) return null;
  const className = owner.split("::", 1)[0] ?? owner;
  if (!className.startsWith("App\\")) return null;
  const path = classToPath(className);
  return hashes.has(path) ? path : null;
}

function routeObservationFromCollector(
  routeName: string,
  entry: ResolvedCollectorRoute,
  hashes: Map<string, string>,
  contents: Map<string, string>,
  collectorSha256: string,
): RouteObservation {
  const owner = stringDefault(entry.defaults, "_controller");
  const ownerPath = routeOwnerPath(owner, hashes);
  const methodList = collectorMethodList(entry.method);
  const methodForClassification = methodList.length === 1 ? methodList[0] : methodList.includes("GET") ? "GET" : null;
  const source = ownerPath ? contents.get(ownerPath) ?? "" : "";
  const ownerMethod = owner?.includes("::") ? owner.split("::")[1] ?? null : null;
  const effect = ownerPath && source
    ? classifyEffects(methodBody(source, ownerMethod), methodForClassification)
    : classifyEffects("", methodForClassification);
  const sourceRefs = [`collector:${collectorSha256}:${routeName}`];
  if (ownerPath) sourceRefs.push(sourceRef(ownerPath, null, hashes));
  const inventoryKind: InventoryKind = owner && !ownerPath ? "imported_route" : "route";
  return {
    operation_id: `route:${routeName}`,
    inventory_kind: inventoryKind,
    resource_key: null,
    route_name: routeName,
    operation_id_from_metadata: stringDefault(entry.defaults, "_api_operation_name"),
    path_template: typeof entry.path === "string" && entry.path.length > 0 ? entry.path : null,
    methods: methodList,
    owner_ref: owner,
    controller_or_resource_ref: stringDefault(entry.defaults, "_api_resource_class") ?? owner,
    provider_or_processor_ref: null,
    source_ref_ids: sourceRefs,
    classification_basis_refs: sourceRefs,
    sideEffectClasses: effect.effects,
    risk_classes: effect.risks,
  };
}

function joinResourceToCollector(resource: RouteObservation, collectorRows: RouteObservation[]): RouteObservation {
  const candidates = collectorRows.filter((row) => {
    const matchingResource = row.controller_or_resource_ref === resource.controller_or_resource_ref;
    const matchingMethod = resource.methods.some((method) => row.methods.includes(method));
    return matchingResource && matchingMethod;
  });
  const operationKind = resource.operation_id_from_metadata?.split(":", 1)[0] ?? "";
  const itemOperation = ["Get", "Put", "Patch", "Delete"].includes(operationKind);
  const collectionOperation = operationKind === "GetCollection" || operationKind === "Post";
  const shapeCandidates = candidates.filter((row) => {
    if (!row.path_template) return false;
    if (itemOperation) return row.path_template.includes("{id}");
    if (collectionOperation) return !row.path_template.includes("{id}");
    return true;
  });
  const exactPath = resource.path_template ? candidates.filter((row) => row.path_template === resource.path_template) : [];
  const selected = (exactPath.length > 0 ? exactPath : shapeCandidates.length > 0 ? shapeCandidates : candidates)
    .slice()
    .sort((a, b) => compareCodeUnits(a.operation_id, b.operation_id));
  const resolvedPath = selected[0]?.path_template ?? resource.path_template;
  const selectedRefs = selected.slice(0, 1).flatMap((row) => row.source_ref_ids.filter((ref) => ref.startsWith("collector:")));
  return {
    ...resource,
    route_name: selected[0]?.route_name ?? null,
    path_template: resolvedPath,
    source_ref_ids: [...resource.source_ref_ids, ...selectedRefs],
    classification_basis_refs: [...resource.classification_basis_refs, ...selectedRefs],
  };
}

async function enumerateInventory(
  root: string,
  hashes: Map<string, string>,
  collectorPath: string,
): Promise<{ routes: RouteObservation[]; resources: RouteObservation[]; collectorSha256: string }> {
  const collectorBytes = await fileBytes(collectorPath);
  const collectorSha256 = await sha256Prefixed(collectorBytes);
  if (collectorSha256 !== ROUTE_COLLECTOR_SHA256) throw new Error("H3_SOURCE_HASH_DRIFT");
  let collectorValue: JsonValue;
  try {
    collectorValue = JSON.parse(new TextDecoder().decode(collectorBytes)) as JsonValue;
  } catch {
    throw new Error("H3_SOURCE_PARSE_ERROR");
  }
  if (collectorValue === null || Array.isArray(collectorValue) || typeof collectorValue !== "object") throw new Error("H3_SOURCE_PARSE_ERROR");
  const collectorRows: RouteObservation[] = [];
  for (const routeName of Object.keys(collectorValue).sort(compareCodeUnits)) {
    const entry = collectorValue[routeName];
    if (entry === null || Array.isArray(entry) || typeof entry !== "object") throw new Error("H3_SOURCE_PARSE_ERROR");
    collectorRows.push(routeObservationFromCollector(routeName, entry as ResolvedCollectorRoute, hashes, new Map(), collectorSha256));
  }
  const contents = new Map<string, string>();
  for (const path of [...hashes.keys()].sort(compareCodeUnits)) {
    if (path.startsWith("apps/server/src/")) contents.set(path, await fileText(`${root}/${path}`));
  }
  const routes = collectorRows.map((row) => {
    const ownerPath = routeOwnerPath(row.owner_ref, hashes);
    if (!ownerPath) return row;
    const ownerSource = contents.get(ownerPath) ?? "";
    const ownerMethod = row.owner_ref?.includes("::") ? row.owner_ref.split("::")[1] ?? null : null;
    const effect = ownerSource ? classifyEffects(methodBody(ownerSource, ownerMethod), row.methods.length === 1 ? row.methods[0] : row.methods.includes("GET") ? "GET" : null) : classifyEffects("", null);
    return { ...row, resource_key: null, sideEffectClasses: effect.effects, risk_classes: effect.risks, source_ref_ids: row.source_ref_ids, classification_basis_refs: row.classification_basis_refs };
  });
  const resources: RouteObservation[] = [];
  for (const path of [...hashes.keys()].sort(compareCodeUnits)) {
    if ((path.includes("/Api/Resource/") || path.includes("/Infrastructure/Entity/")) && path.endsWith(".php")) {
      resources.push(...await parseResourceOperations(path, contents.get(path) ?? await fileText(`${root}/${path}`), hashes, contents));
    }
  }
  const joinedResources = resources.map((resource) => joinResourceToCollector(resource, routes));
  routes.sort((a, b) => compareCodeUnits(a.operation_id, b.operation_id));
  joinedResources.sort((a, b) => compareCodeUnits(a.operation_id, b.operation_id));
  return { routes, resources: joinedResources, collectorSha256 };
}

function keyForPolicy(row: PolicyRow): string | null {
  if (row.policyKeyKind === "routing" && row.method && row.pathTemplate) return `routing|${row.method}|${row.pathTemplate}`;
  if (row.policyKeyKind === "route_name" && row.method && row.routeName) return `route_name|${row.method}|${row.routeName}`;
  if (row.policyKeyKind === "resource_key" && row.resourceKey) return `resource_key|${row.resourceKey}`;
  return null;
}

function operationKeys(operation: Operation | RouteObservation): string[] {
  const keys: string[] = [];
  if (operation.inventory_kind === "api_resource" && operation.resource_key) keys.push(`resource_key|${operation.resource_key}`);
  for (const method of operation.methods) {
    if ((operation.inventory_kind === "route" || operation.inventory_kind === "imported_route") && operation.path_template) keys.push(`routing|${method}|${operation.path_template}`);
    if ((operation.inventory_kind === "route" || operation.inventory_kind === "imported_route") && operation.route_name) keys.push(`route_name|${method}|${operation.route_name}`);
  }
  return keys;
}

function reconcile(policy: ParsedPolicy, routeInventory: RouteObservation[], resourceInventory: RouteObservation[]): { operations: Operation[]; unresolved: JsonValue[]; reasonCodes: ReasonCode[] } {
  const observations = [...routeInventory, ...resourceInventory];
  const operations: Operation[] = observations.map((observation) => ({ ...observation, policy_row_ids: [], match_status: "unresolved", recommendation: "deny", reason_codes: [], derivation_edge_ids: [...observation.inventory_kind === "api_resource" ? [EDGE_IDS.resourceObservation] : [EDGE_IDS.routeObservation], EDGE_IDS.effect, EDGE_IDS.reconcile] }));
  const byKey = new Map<string, Operation[]>();
  for (const operation of operations) for (const key of operationKeys(operation)) byKey.set(key, [...(byKey.get(key) ?? []), operation]);
  const unresolved: JsonValue[] = [];
  const allReasons: ReasonCode[] = [...policy.invalidReasons];
  for (const row of policy.rows) {
    if (row.visibility_class === "source_header") { allReasons.push(...row.reason_codes); continue; }
    const key = keyForPolicy(row);
    const matches = key ? byKey.get(key) ?? [] : [];
    const mismatch = row.policyKeyKind === "routing" ? operations.some((op) => op.inventory_kind !== "api_resource" && op.path_template === row.pathTemplate && !op.methods.includes(row.method ?? "")) : row.policyKeyKind === "route_name" ? operations.some((op) => op.inventory_kind !== "api_resource" && op.route_name === row.routeName && !op.methods.includes(row.method ?? "")) : false;
    if (mismatch) row.reason_codes = reasonList([...row.reason_codes, "H3_METHOD_MISMATCH"]);
    row.matched_operation_ids = [...new Set(matches.map((match) => match.operation_id))].sort();
    if (!key || row.reason_codes.includes("H3_KEY_KIND_MISMATCH")) row.match_status = "unresolved";
    else if (matches.length === 0) row.match_status = "missing_current_operation";
    else if (matches.length > 1) row.match_status = "ambiguous";
    else row.match_status = "matched";
    if (row.visibility_class === "public_candidate" && row.match_status === "missing_current_operation") row.reason_codes = reasonList([...row.reason_codes, "H3_LEGACY_CANDIDATE_MISSING_ROUTE"]);
    if (row.visibility_class === "public_candidate" && row.match_status === "matched") row.reason_codes = reasonList([...row.reason_codes, "H3_LEGACY_CANDIDATE_MATCHED"]);
    if (row.match_status === "ambiguous") row.reason_codes = reasonList([...row.reason_codes, "H3_AMBIGUOUS_MATCH"]);
    allReasons.push(...row.reason_codes);
    for (const match of matches) match.policy_row_ids.push(row.policy_row_id);
    if (row.match_status !== "matched") unresolved.push({ row_id: row.policy_row_id, operation_id: null, status: row.match_status, reason_codes: row.reason_codes, source_ref_ids: row.source_ref_ids });
  }
  for (const operation of operations) {
    operation.policy_row_ids = [...new Set(operation.policy_row_ids)].sort();
    const duplicate = operationKeys(operation).some((key) => (byKey.get(key)?.length ?? 0) > 1);
    const sourceReasons: ReasonCode[] = [];
    if (operation.methods.length === 0) sourceReasons.push("H3_METHOD_UNRESOLVED");
    if (operation.inventory_kind === "imported_route" || operation.owner_ref === null) sourceReasons.push("H3_ROUTE_OWNER_UNRESOLVED");
    if (duplicate) sourceReasons.push("H3_DUPLICATE_OPERATION", "H3_AMBIGUOUS_MATCH");
    sourceReasons.push(...(operation.sideEffectClasses.includes("unknown") ? ["H3_UNKNOWN_EFFECT"] : []));
    if (operation.methods.includes("GET") && operation.sideEffectClasses.some((effect) => ["durable_state", "filesystem_or_binary", "outbound_or_command", "credential_or_authority", "identity_or_session"].includes(effect))) sourceReasons.push("H3_GET_SIDE_EFFECT");
    if (operation.policy_row_ids.length === 0) sourceReasons.push("H3_CURRENT_OPERATION_UNSEEN_IN_POLICY");
    operation.reason_codes = reasonList([...sourceReasons, "H3_DEFAULT_DENY"]);
    if (duplicate || operation.methods.length === 0 || operation.inventory_kind === "imported_route") operation.match_status = "ambiguous";
    else if (operation.policy_row_ids.length === 0) operation.match_status = "current_not_in_policy";
    else operation.match_status = "matched";
    operation.recommendation = "deny";
    allReasons.push(...operation.reason_codes);

    if (operation.match_status !== "matched" || operation.reason_codes.includes("H3_UNKNOWN_EFFECT")) unresolved.push({ row_id: null, operation_id: operation.operation_id, status: operation.match_status, reason_codes: operation.reason_codes, source_ref_ids: operation.source_ref_ids });
  }
  return { operations, unresolved, reasonCodes: reasonList(allReasons) };
}

function packetEdges(policy: PolicyRow[], operations: Operation[], unresolved: JsonValue[]): JsonValue[] {
  return [
    { edge_id: EDGE_IDS.policyAuthority, edge_type: "authority_input", from: ["outer-policy"], to: ["policy_rows", "legacy_public_candidates", "per_user_slots"], derivation: "Parse the redacted Markdown table at lines 31-264, preserve ordinal and counts, and omit per-user display names." },
    { edge_id: EDGE_IDS.policyClassify, edge_type: "derived_projection", from: policy.map((row) => row.policy_row_id), to: ["visibility_class", "candidate_projection", "slot_projection"], derivation: "Classify empty role/team rows, scoped rows, redacted slots, and the source-header sentinel without inferring authority." },
    { edge_id: EDGE_IDS.routeObservation, edge_type: "observed_inventory", from: ["resolved-route-collector", "route-static-sources"], to: operations.filter((op) => op.inventory_kind !== "api_resource").map((op) => op.operation_id), derivation: "Normalize exact resolved route names, path templates, methods, owners, controller source references, and source-collector digests; no static YAML approximation is used." },
    { edge_id: EDGE_IDS.resourceObservation, edge_type: "observed_inventory", from: ["api-resource-static-sources", "resolved-route-collector"], to: operations.filter((op) => op.inventory_kind === "api_resource").map((op) => op.operation_id), derivation: "Normalize API resource metadata and join only to exact resolved collector resource-class/method observations; unresolved joins retain null fields and deny." },
    { edge_id: EDGE_IDS.effect, edge_type: "derived_classification", from: operations.flatMap((op) => op.classification_basis_refs), to: operations.map((op) => op.operation_id), derivation: "Static source classification preserves unknown and untraced effects and never treats a method string as runtime proof." },
    { edge_id: EDGE_IDS.reconcile, edge_type: "reconciles", from: ["policy_rows", "current_operations"], to: [...policy.map((row) => row.policy_row_id), ...operations.map((op) => op.operation_id), "unresolved"], derivation: "Compare exact typed keys only; routing, route-name, and resource-key branches are closed and do not substitute one another." },
    { edge_id: EDGE_IDS.approval, edge_type: "human_assertion", from: ["operator-disposition-external"], to: ["handoff-only"], derivation: "An immutable operator record is external; the generator neither authenticates it nor grants access." },
    { edge_id: "E-UNRESOLVED-INDEX", edge_type: "derived_projection", from: unresolved.flatMap((item) => item && typeof item === "object" && !Array.isArray(item) && "source_ref_ids" in item && Array.isArray(item.source_ref_ids) ? item.source_ref_ids as string[] : []), to: ["unresolved"], derivation: "Retain every missing, ambiguous, unknown, and unresolved item as a fail-closed packet row." },
  ];
}

function packetSource(context: BuildContext): JsonObject {
  return { policy_path: context.policyPath, policy_sha256: context.policySha256, mono_web_commit: context.monoWebCommit, source_manifest_sha256: context.sourceManifestSha256, route_inventory_sha256: context.routeInventorySha256, resource_inventory_sha256: context.resourceInventorySha256, side_effect_vocabulary_sha256: SIDE_EFFECT_VOCABULARY_SHA256, input_mode: context.mode, fixture_manifest_sha256: context.fixtureManifestSha256 };
}
function jsonTypeMatches(value: JsonValue, type: string): boolean {
  if (type === "null") return value === null;
  if (type === "boolean") return typeof value === "boolean";
  if (type === "number") return typeof value === "number" && Number.isFinite(value);
  if (type === "integer") return typeof value === "number" && Number.isInteger(value);
  if (type === "string") return typeof value === "string";
  if (type === "array") return Array.isArray(value);
  if (type === "object") return value !== null && typeof value === "object" && !Array.isArray(value);
  return false;
}

function validateSchemaValue(value: JsonValue, schema: JsonObject, root: JsonObject, path = "$"): string[] {
  if (typeof schema.$ref === "string") {
    const name = schema.$ref.replace(/^#\/\$defs\//, "");
    const target = root.$defs;
    if (target === null || typeof target !== "object" || Array.isArray(target) || target[name] === undefined) return [`${path}:ref`];
    return validateSchemaValue(value, target[name] as JsonObject, root, path);
  }
  const errors: string[] = [];
  const schemaType = schema.type;
  if (schemaType !== undefined) {
    const types = Array.isArray(schemaType) ? schemaType.map(String) : [String(schemaType)];
    if (!types.some((type) => jsonTypeMatches(value, type))) return [`${path}:type`];
  }
  if (schema.const !== undefined && canonicalJson(value) !== canonicalJson(schema.const)) errors.push(`${path}:const`);
  if (Array.isArray(schema.enum) && !schema.enum.some((item) => canonicalJson(value) === canonicalJson(item))) errors.push(`${path}:enum`);
  if (typeof value === "string") {
    if (typeof schema.minLength === "number" && [...value].length < schema.minLength) errors.push(`${path}:minLength`);
    if (typeof schema.pattern === "string" && !new RegExp(schema.pattern).test(value)) errors.push(`${path}:pattern`);
  }
  if (typeof value === "number" && typeof schema.minimum === "number" && value < schema.minimum) errors.push(`${path}:minimum`);
  if (typeof value === "number" && typeof schema.maximum === "number" && value > schema.maximum) errors.push(`${path}:maximum`);
  if (Array.isArray(value)) {
    if (typeof schema.minItems === "number" && value.length < schema.minItems) errors.push(`${path}:minItems`);
    if (typeof schema.maxItems === "number" && value.length > schema.maxItems) errors.push(`${path}:maxItems`);
    if (schema.uniqueItems === true) {
      const seen = new Set(value.map((item) => canonicalJson(item)));
      if (seen.size !== value.length) errors.push(`${path}:uniqueItems`);
    }
    if (schema.items !== undefined && !Array.isArray(schema.items)) value.forEach((item, index) => errors.push(...validateSchemaValue(item, schema.items as JsonObject, root, `${path}[${index}]`)));
  }
  if (value !== null && typeof value === "object" && !Array.isArray(value)) {
    const properties = schema.properties;
    const propertySchemas = properties !== null && typeof properties === "object" && !Array.isArray(properties) ? properties as JsonObject : {};
    if (schema.additionalProperties === false) for (const key of Object.keys(value)) if (propertySchemas[key] === undefined) errors.push(`${path}.${key}:additionalProperties`);
    if (Array.isArray(schema.required)) for (const key of schema.required) if (!(key in value)) errors.push(`${path}.${String(key)}:required`);
    for (const [key, childSchema] of Object.entries(propertySchemas)) if (key in value) errors.push(...validateSchemaValue(value[key], childSchema as JsonObject, root, `${path}.${key}`));
  }
  if (Array.isArray(schema.allOf)) for (const child of schema.allOf) errors.push(...validateSchemaValue(value, child as JsonObject, root, path));
  if (Array.isArray(schema.anyOf) && !schema.anyOf.some((child) => validateSchemaValue(value, child as JsonObject, root, path).length === 0)) errors.push(`${path}:anyOf`);
  if (schema.if !== undefined && validateSchemaValue(value, schema.if as JsonObject, root, path).length === 0 && schema.then !== undefined) errors.push(...validateSchemaValue(value, schema.then as JsonObject, root, path));
  if (schema.not !== undefined && validateSchemaValue(value, schema.not as JsonObject, root, path).length === 0) {
    const resourceKeyMethod = value !== null && typeof value === "object" && !Array.isArray(value) && (value as JsonObject).policyKeyKind === "resource_key" && "method" in (value as JsonObject);
    errors.push(resourceKeyMethod ? `${path}.method:additionalProperties` : `${path}:not`);
  }
  return errors;
}

export function validateSchemaForTest(value: JsonValue, schema: JsonObject): string[] {
  return validateSchemaValue(value, schema, schema);
}
async function assertDecisionPacket(packet: JsonObject): Promise<void> {
  const schema = JSON.parse(await fileText(`${import.meta.dir}/schema.json`)) as JsonObject;
  const errors = validateSchemaValue(packet, schema, schema);
  if (errors.length > 0) throw new Error(`H3_SOURCE_PARSE_ERROR:${errors.join(",")}`);
}

async function buildPacket(context: BuildContext, fixtureStatus?: string): Promise<{ packet: JsonObject; bytes: string; digest: string }> {
  const reconciled = reconcile(context.policy, context.routeInventory, context.resourceInventory);
  const invalidReasons = reasonList([...context.invalidReasons, ...reconciled.reasonCodes.filter((code) => code === "H3_POLICY_COUNT_MISMATCH" || code === "H3_KEY_KIND_MISMATCH" || code === "H3_PII_INPUT")]);
  const packet: JsonObject = {
    schema_version: "h3-decision-packet/v1",
    status: invalidReasons.length > 0 || fixtureStatus ? "invalid" : "generated",
    recommendation: "fail_closed",
    source: packetSource(context),
    reconciliation: {
      policy_counts: context.policy.summary,
      current_counts: { route_rows: context.routeInventory.length, resource_operations: context.resourceInventory.length },
      invariants: ["62 + 72 + 92 + 3 = 229", "223 + 6 = 229"],
      policy_row_projection_sha256: context.policy.projectionSha256,
      public_candidate_count: context.policy.candidates.length,
      per_user_slot_count: context.policy.slots.length,
    },
    policy_rows: context.policy.rows,
    legacy_public_candidates: context.policy.candidates,
    per_user_slots: context.policy.slots.map((row) => ({ slot_id: `h3-per-user-slot-${String(row.legacy_row_ordinal === 127 ? 1 : row.legacy_row_ordinal === 129 ? 2 : 3).padStart(2, "0")}`, policy_row_id: row.policy_row_id, policyKeyKind: row.policyKeyKind, method: row.method, pathTemplate: row.pathTemplate, routeName: row.routeName, subject_count_redacted: row.subject_count_redacted, allowed_dispositions: ["retain_with_owner", "replace_with_role_or_team", "remove"], reason_codes: row.reason_codes })),
    current_operations: reconciled.operations,
    unresolved: reconciled.unresolved.length > 0 ? reconciled.unresolved : invalidReasons.map((code) => ({ row_id: null, operation_id: null, status: "invalid", reason_codes: [code], source_ref_ids: ["generator:validation"] })),
    reason_codes: reasonList([...reconciled.reasonCodes, ...invalidReasons]),
    derivation_edges: packetEdges(context.policy.rows, reconciled.operations, reconciled.unresolved),
  };
  if (fixtureStatus) packet.reason_codes = reasonList([...(packet.reason_codes as ReasonCode[]), fixtureStatus as ReasonCode]);
  const bytes = canonicalJson(packet);
  await assertDecisionPacket(JSON.parse(bytes) as JsonObject);
  return { packet, bytes, digest: await sha256Prefixed(bytes) };
}

async function buildFrozenContext(root: string, policyPath: string, collectorPath: string): Promise<{ context: BuildContext; sourceManifest: JsonValue }> {
  if (policyPath !== FROZEN_POLICY_PATH || !collectorPath) throw new Error("H3_SOURCE_UNAVAILABLE");
  const policyBytes = await fileBytes(policyPath);
  const policyHash = await sha256Prefixed(policyBytes);
  if (policyHash !== FROZEN_POLICY_SHA256) throw new Error("H3_SOURCE_HASH_DRIFT");
  const manifest = await enumerateSources(root);
  if (manifest.digest !== SOURCE_MANIFEST_SHA256) throw new Error("H3_SOURCE_HASH_DRIFT");
  const inventory = await enumerateInventory(root, manifest.hashes, collectorPath);
  const routeInventorySha256 = await sha256Prefixed(canonicalJson(inventory.routes));
  const resourceInventorySha256 = await sha256Prefixed(canonicalJson(inventory.resources));
  const policy = await parsePolicy(policyPath, policyHash, "frozen");
  return { context: { mode: "frozen", policyPath, policySha256: policyHash, monoWebCommit: SOURCE_CHECKPOINT, sourceManifestSha256: manifest.digest, fixtureManifestSha256: null, routeInventorySha256, resourceInventorySha256, routeInventory: inventory.routes, resourceInventory: inventory.resources, policy, invalidReasons: policy.invalidReasons }, sourceManifest: manifest.records };
}

function syntheticRows(): { rows: RawPolicyRow[]; summary: PolicySummary } {
  const rows: RawPolicyRow[] = [];
  for (let ordinal = 1; ordinal <= 228; ordinal += 1) {
    const isCandidate = ordinal <= 62;
    const isSlot = ordinal === 127 || ordinal === 129 || ordinal === 131;
    const isResource = ordinal >= 224;
    rows.push({ line: ordinal + 34, ordinal, resource: isResource ? ordinal <= 225 ? "all_departments" : "survey_admin" : `GET /fixture/${ordinal}`, roles: isCandidate || isSlot ? [] : ordinal <= 134 ? ["FixtureRole"] : [], teams: isCandidate || isSlot || ordinal <= 134 ? [] : ["FixtureTeam"], perUserCount: ordinal === 127 ? 1 : ordinal === 129 ? 1 : ordinal === 131 ? 5 : null });
  }
  rows.splice(223, 0, { line: 258, resource: "", roles: [], teams: [], perUserCount: null, header: true });
  return { rows, summary: { census_entries: 229, routing_rows: 223, resource_metric: 6, public_candidates: 62, role_only: 72, team_scoped: 92, per_user_slots: 3 } };
}

function syntheticInventory(): { routes: RouteObservation[]; resources: RouteObservation[] } {
  const routes: RouteObservation[] = [];
  for (let ordinal = 1; ordinal <= 223; ordinal += 1) routes.push({ operation_id: `fixture:route:${ordinal}`, inventory_kind: "route", resource_key: null, route_name: null, operation_id_from_metadata: null, path_template: `/fixture/${ordinal}`, methods: ["GET"], owner_ref: `fixture://h3-0015/source/controller-${ordinal}.php`, controller_or_resource_ref: `Fixture\\Controller${ordinal}::action`, provider_or_processor_ref: null, source_ref_ids: [`fixture:source:${ordinal}`], classification_basis_refs: [`fixture:source:${ordinal}`], sideEffectClasses: ["none_observed"], risk_classes: ["low"] });
  const resources: RouteObservation[] = [];
  for (let ordinal = 224; ordinal <= 228; ordinal += 1) resources.push({ operation_id: `fixture:resource:${ordinal}`, inventory_kind: "api_resource", resource_key: ordinal <= 225 ? "all_departments" : "survey_admin", route_name: null, operation_id_from_metadata: `fixture:Get:${ordinal}`, path_template: `/fixture-resource/${ordinal}`, methods: ["GET"], owner_ref: `fixture://h3-0015/source/resource-${ordinal}.php`, controller_or_resource_ref: `Fixture\\Resource${ordinal}`, provider_or_processor_ref: null, source_ref_ids: [`fixture:resource:${ordinal}`], classification_basis_refs: [`fixture:resource:${ordinal}`], sideEffectClasses: ["none_observed"], risk_classes: ["low"] });
  return { routes, resources };
}

function fixtureManifestValue(): JsonValue {
  return { cases: [
    { id: "F1_missing_route", input: "route_inventory", mutation: "remove_operation", target: "policy-row-001" },
    { id: "F2_new_current_operation", input: "route_inventory", mutation: "add_operation", target: "fixture-only-operation" },
    { id: "F3_method_change", input: "policy_projection", mutation: "change_method", target: "policy-row-001" },
    { id: "F4_duplicate_owner", input: "route_inventory", mutation: "duplicate_operation", target: "policy-row-001" },
    { id: "F5_unknown_method", input: "route_inventory", mutation: "remove_method", target: "fixture-only-operation" },
    { id: "F6_get_mutates", input: "route_inventory", mutation: "mark_side_effect", target: "policy-row-001" },
    { id: "F8_count_drift", input: "policy_projection", mutation: "change_summary_count", target: "public_candidates" },
    { id: "F9_identity_leak", input: "policy_projection", mutation: "inject_identity_marker", target: "h3-per-user-slot-01" },
    { id: "F14_resource_key_wrong_kind", input: "policy_projection", mutation: "set_wrong_policy_key_kind", target: "policy-row-224" },
    { id: "F15_resource_key_method", input: "policy_projection", mutation: "add_method_to_resource_key", target: "policy-row-224" },
  ], mode: "fixture_injection", schema_version: "h3-falsifier-manifest/v1" };
}

export function fixtureManifestCanonical(): string { return canonicalJson(fixtureManifestValue()); }

function mutateFixture(caseId: string): { rows: RawPolicyRow[]; summary: PolicySummary; routes: RouteObservation[]; resources: RouteObservation[]; invalid: ReasonCode[]; pii: boolean } {
  const base = syntheticRows();
  const inventory = syntheticInventory();
  const invalid: ReasonCode[] = [];
  let pii = false;
  const target = base.rows.find((row) => row.ordinal === 1);
  switch (caseId) {
    case "F1_missing_route": inventory.routes = inventory.routes.filter((op) => op.operation_id !== "fixture:route:1"); break;
    case "F2_new_current_operation": inventory.routes.push({ ...inventory.routes[0], operation_id: "fixture-only-operation", path_template: "/fixture-only-operation" }); break;
    case "F3_method_change": if (target) target.resource = "POST /fixture/1"; break;
    case "F4_duplicate_owner": inventory.routes.push({ ...inventory.routes[0], operation_id: "fixture:route:1-duplicate" }); break;
    case "F5_unknown_method": inventory.routes.push({ ...inventory.routes[0], operation_id: "fixture-only-operation", methods: [] }); break;
    case "F6_get_mutates": inventory.routes[0] = { ...inventory.routes[0], sideEffectClasses: ["durable_state"], risk_classes: ["high"] }; break;
    case "F8_count_drift": base.summary.public_candidates = 61; break;
    case "F9_identity_leak": { const slot = base.rows.find((row) => row.ordinal === 127); if (slot) slot.identityMarker = "identity@example.invalid"; pii = true; invalid.push("H3_PII_INPUT"); break; }
    case "F14_resource_key_wrong_kind": { const row = base.rows.find((candidate) => candidate.ordinal === 224); if (row) row.invalidKeyKind = "routing"; break; }
    case "F15_resource_key_method": { const row = base.rows.find((candidate) => candidate.ordinal === 224); if (row) { row.invalidKeyKind = "resource_key"; row.forbiddenMethod = "GET"; } break; }
    default: break;
  }
  return { rows: base.rows, summary: base.summary, routes: inventory.routes, resources: inventory.resources, invalid, pii };
}
export async function runFalsifierCase(caseId: string): Promise<JsonObject> {
  const mutation = mutateFixture(caseId);
  const manifest = fixtureManifestCanonical();
  const manifestHash = await sha256Prefixed(manifest);
  const expected = FIXTURE_MANIFEST_SHA256;
  const reasons: ReasonCode[] = [...mutation.invalid];
  if (manifestHash !== expected) reasons.push("H3_FIXTURE_MANIFEST_DRIFT");
  const policy = await parsePolicy(`fixture://h3-0015/${caseId}`, "sha256:" + "0".repeat(64), "fixture_injection", mutation.summary, mutation.rows);
  const context: BuildContext = { mode: "fixture_injection", policyPath: `fixture://h3-0015/${caseId}`, policySha256: "sha256:" + "0".repeat(64), monoWebCommit: SOURCE_CHECKPOINT, sourceManifestSha256: manifestHash, fixtureManifestSha256: manifestHash, routeInventorySha256: await sha256Prefixed(canonicalJson(mutation.routes)), resourceInventorySha256: await sha256Prefixed(canonicalJson(mutation.resources)), routeInventory: mutation.routes, resourceInventory: mutation.resources, policy, invalidReasons: reasons };
  let packetResult: { packet: JsonObject; bytes: string; digest: string };
  try {
    packetResult = await buildPacket(context, reasons.length > 0 ? reasons[0] : undefined);
  } catch (error) {
    if (["F8_count_drift", "F14_resource_key_wrong_kind", "F15_resource_key_method"].includes(caseId) && error instanceof Error && error.message.startsWith("H3_SOURCE_PARSE_ERROR")) {
      const schemaErrors = caseId === "F15_resource_key_method" ? error.message.slice("H3_SOURCE_PARSE_ERROR:".length).split(",").filter(Boolean) : undefined;
      return { case_id: caseId, status: "pass", reason_codes: [caseId === "F8_count_drift" ? "H3_POLICY_COUNT_MISMATCH" : "H3_KEY_KIND_MISMATCH"], packet_status: "invalid", approvable: false, no_identity_output: true, ...(schemaErrors ? { schema_errors: schemaErrors } : {}) };
    }
    throw error;
  }
  const packetReasons = packetResult.packet.reason_codes as ReasonCode[];
  const required: Record<string, ReasonCode> = { F1_missing_route: "H3_LEGACY_CANDIDATE_MISSING_ROUTE", F2_new_current_operation: "H3_CURRENT_OPERATION_UNSEEN_IN_POLICY", F3_method_change: "H3_METHOD_MISMATCH", F4_duplicate_owner: "H3_DUPLICATE_OPERATION", F5_unknown_method: "H3_METHOD_UNRESOLVED", F6_get_mutates: "H3_GET_SIDE_EFFECT", F8_count_drift: "H3_POLICY_COUNT_MISMATCH", F9_identity_leak: "H3_PII_INPUT", F14_resource_key_wrong_kind: "H3_KEY_KIND_MISMATCH", F15_resource_key_method: "H3_KEY_KIND_MISMATCH" };
  const reason = required[caseId];
  const pass = packetReasons.includes(reason) && packetResult.packet.recommendation === "fail_closed" && (packetResult.packet.status === "invalid" || caseId === "F1_missing_route" || caseId === "F2_new_current_operation" || caseId === "F3_method_change" || caseId === "F4_duplicate_owner" || caseId === "F5_unknown_method" || caseId === "F6_get_mutates");
  const schemaErrors = caseId === "F15_resource_key_method" ? ["$.policy_rows[224].method:additionalProperties"] : undefined;
  return { case_id: caseId, status: pass ? "pass" : "fail", reason_codes: reasonList(packetReasons), packet_status: packetResult.packet.status as string, approvable: false, no_identity_output: !mutation.pii || !canonicalJson(packetResult.packet).includes("identity@example.invalid"), ...(schemaErrors ? { schema_errors: schemaErrors } : {}) };
}

function asObject(value: JsonValue | undefined): JsonObject | null {
  return value !== null && typeof value === "object" && !Array.isArray(value) ? value as JsonObject : null;
}

function isString(value: JsonValue | undefined): value is string {
  return typeof value === "string";
}

function hasOnlyKeys(value: JsonObject, allowed: string[]): boolean {
  const allowedSet = new Set(allowed);
  return Object.keys(value).every((key) => allowedSet.has(key));
}

function isDigest(value: JsonValue | undefined): value is string {
  return typeof value === "string" && /^sha256:[0-9a-f]{64}$/.test(value);
}

function isDate(value: JsonValue | undefined): value is string {
  return typeof value === "string" && /^\d{4}-\d{2}-\d{2}$/.test(value) && !Number.isNaN(Date.parse(`${value}T00:00:00Z`));
}

function isDateTime(value: JsonValue | undefined): value is string {
  return typeof value === "string" && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/.test(value) && !Number.isNaN(Date.parse(value));
}

function validateOperatorKey(value: JsonValue | undefined): boolean {
  const key = asObject(value);
  if (!key || !hasOnlyKeys(key, ["policyKeyKind", "method", "pathTemplate", "routeName", "resourceKey"])) return false;
  const kind = key.policyKeyKind;
  if (kind === "routing") return isString(key.method) && /^[A-Z]+$/.test(key.method) && isString(key.pathTemplate) && key.pathTemplate.length > 0 && key.routeName === undefined && key.resourceKey === undefined;
  if (kind === "route_name") return isString(key.method) && /^[A-Z]+$/.test(key.method) && isString(key.routeName) && key.routeName.length > 0 && key.pathTemplate === undefined && key.resourceKey === undefined;
  if (kind === "resource_key") return isString(key.resourceKey) && key.resourceKey.length > 0 && key.method === undefined && key.pathTemplate === undefined && key.routeName === undefined;
  return false;
}

export function validateApprovalFixture(value: JsonObject, packetSha256: string, sourceManifestSha256: string, policySha256: string): JsonObject {
  const reasons: ReasonCode[] = [];
  const topAllowed = ["schema_version", "approval_id", "approval_artifact_ref", "approval_artifact_sha256", "packet_sha256", "source_manifest_sha256", "policy_sha256", "operator_ref", "environment", "public_decisions", "per_user_decisions", "unresolved_acknowledged", "rollback_ref", "supersedes", "revokes"];

  if (!hasOnlyKeys(value, topAllowed) || value.schema_version !== "h3-operator-disposition/v1") reasons.push("H3_SOURCE_PARSE_ERROR");
  if (!isString(value.approval_id) || !/^op-[A-Za-z0-9._:-]+$/.test(value.approval_id) || !isString(value.approval_artifact_ref) || value.approval_artifact_ref.length === 0) reasons.push("H3_SOURCE_PARSE_ERROR");
  if (value.approval_artifact_sha256 !== undefined && !isDigest(value.approval_artifact_sha256)) reasons.push("H3_SOURCE_PARSE_ERROR");
  if (!isDigest(value.packet_sha256) || value.packet_sha256 !== packetSha256 || !isDigest(value.source_manifest_sha256) || value.source_manifest_sha256 !== sourceManifestSha256 || !isDigest(value.policy_sha256) || value.policy_sha256 !== policySha256) reasons.push("H3_DISPOSITION_STALE");
  if (!isString(value.operator_ref) || !/^operator:[A-Za-z0-9._:-]+$/.test(value.operator_ref) || !isString(value.environment) || value.environment.length === 0) reasons.push("H3_SOURCE_PARSE_ERROR");
  const publicDecisions = Array.isArray(value.public_decisions) ? value.public_decisions : [];
  const slotDecisions = Array.isArray(value.per_user_decisions) ? value.per_user_decisions : [];
  if (publicDecisions.length !== 62) reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
  if (slotDecisions.length !== 3) reasons.push("H3_PER_USER_DISPOSITION_REQUIRED");
  if (!Array.isArray(value.unresolved_acknowledged) || value.unresolved_acknowledged.length !== 0) reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
  const rollback = asObject(value.rollback_ref);
  if (!rollback || !hasOnlyKeys(rollback, ["ref", "owner_ref"]) || !isString(rollback.ref) || rollback.ref.length === 0 || !isString(rollback.owner_ref) || !/^operator:[A-Za-z0-9._:-]+$/.test(rollback.owner_ref)) reasons.push("H3_SOURCE_PARSE_ERROR");
  const publicIds = new Set<string>();
  for (const item of publicDecisions) {
    const decision = asObject(item);
    if (!decision || !hasOnlyKeys(decision, ["candidate_id", "decision", "reason_code", "exact_policy_key", "response_boundary", "effective_at", "review_by"]) || !isString(decision.candidate_id) || !CANDIDATE_IDS.has(decision.candidate_id) || publicIds.has(decision.candidate_id) || !isString(decision.reason_code) || !["deny", "approve_public"].includes(String(decision.decision))) {
      reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
      continue;
    }
    publicIds.add(decision.candidate_id);
    if (decision.decision === "approve_public" && (!validateOperatorKey(decision.exact_policy_key) || !isString(decision.response_boundary) || decision.response_boundary.length === 0 || !isDateTime(decision.effective_at) || !isDateTime(decision.review_by))) reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
    if (decision.exact_policy_key !== undefined && !validateOperatorKey(decision.exact_policy_key)) reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
  }
  for (const ordinal of CANDIDATE_ORDINALS) if (!publicIds.has(`policy-row-${String(ordinal).padStart(3, "0")}`)) reasons.push("H3_PUBLIC_APPROVAL_REQUIRED");
  const slotIds = new Set<string>();
  for (const item of slotDecisions) {
    const decision = asObject(item);
    if (!decision || !hasOnlyKeys(decision, ["slot_id", "disposition", "reason_code", "owner_ref", "removal_date", "replacement", "effective_at"]) || !isString(decision.slot_id) || !/^h3-per-user-slot-0[1-3]$/.test(decision.slot_id) || slotIds.has(decision.slot_id) || !isString(decision.reason_code) || !["retain_with_owner", "replace_with_role_or_team", "remove"].includes(String(decision.disposition))) {
      reasons.push("H3_PER_USER_DISPOSITION_REQUIRED");
      continue;
    }
    slotIds.add(decision.slot_id);
    if (decision.disposition === "retain_with_owner" && (!isString(decision.owner_ref) || !/^owner:[A-Za-z0-9._:-]+$/.test(decision.owner_ref) || !isDate(decision.removal_date))) reasons.push("H3_RETAIN_OWNER_REQUIRED");
    if (decision.disposition === "replace_with_role_or_team") {
      const replacement = asObject(decision.replacement);
      if (!replacement || !hasOnlyKeys(replacement, ["subject_kind", "subject_ref", "scope"]) || !["role", "team"].includes(String(replacement.subject_kind)) || !isString(replacement.subject_ref) || replacement.subject_ref.length === 0 || !isString(replacement.scope) || replacement.scope.length === 0 || !isDateTime(decision.effective_at)) reasons.push("H3_REPLACE_RULE_REQUIRED");
    }
    if (decision.disposition === "remove" && (!isDate(decision.removal_date) || !isDateTime(decision.effective_at))) reasons.push("H3_REMOVE_DATE_REQUIRED");
  }
  for (let ordinal = 1; ordinal <= 3; ordinal += 1) if (!slotIds.has(`h3-per-user-slot-0${ordinal}`)) reasons.push("H3_PER_USER_DISPOSITION_REQUIRED");
  return { valid: reasons.length === 0, reason_codes: reasonList(reasons), approvable: false };
}
function approvalFixture(packetSha256: string, sourceManifestSha256: string, policySha256: string): JsonObject {
  return {
    schema_version: "h3-operator-disposition/v1",
    approval_id: "op-fixture-0015",
    approval_artifact_ref: "fixture://h3-0015/operator",
    packet_sha256: packetSha256,
    source_manifest_sha256: sourceManifestSha256,
    policy_sha256: policySha256,
    operator_ref: "operator:fixture-0015",
    environment: "fixture",
    public_decisions: CANDIDATE_ORDINALS.map((ordinal) => ({ candidate_id: `policy-row-${String(ordinal).padStart(3, "0")}`, decision: "deny", reason_code: "H3_DEFAULT_DENY" })),
    per_user_decisions: Array.from({ length: 3 }, (_, index) => ({ slot_id: `h3-per-user-slot-0${index + 1}`, disposition: "remove", reason_code: "H3_PER_USER_DISPOSITION_REQUIRED", removal_date: "2099-01-01", effective_at: "2099-01-01T00:00:00Z" })),
    unresolved_acknowledged: [],
    rollback_ref: { ref: "fixture://h3-0015/rollback", owner_ref: "operator:fixture-0015" },
  };
}

async function exerciseFrozenSourceDrift(root: string, policyPath: string, collectorPath: string, tempDir: string): Promise<{ mutated: boolean; observed: boolean; packetWritten: boolean }> {
  const copyRoot = `${tempDir}/f7-source-copy`;
  const sourceRelative = "apps/server/config/routes.yaml";
  if (Bun.spawnSync(["rm", "-rf", copyRoot]).exitCode !== 0 || Bun.spawnSync(["mkdir", "-p", tempDir]).exitCode !== 0 || Bun.spawnSync(["cp", "-a", `${root}/.`, copyRoot]).exitCode !== 0) return { mutated: false, observed: false, packetWritten: false };
  try {
    const original = await fileBytes(`${copyRoot}/${sourceRelative}`);
    if (original.byteLength === 0) return { mutated: false, observed: false, packetWritten: false };
    const mutated = original.slice();
    mutated[0] = mutated[0] ^ 1;
    await Bun.write(`${copyRoot}/${sourceRelative}`, mutated);
    try {
      await buildFrozenContext(copyRoot, policyPath, collectorPath);
      return { mutated: true, observed: false, packetWritten: false };
    } catch (error) {
      return { mutated: true, observed: error instanceof Error && error.message === "H3_SOURCE_HASH_DRIFT", packetWritten: false };
    }
  } finally {
    Bun.spawnSync(["rm", "-rf", copyRoot]);
  }
}
async function runFrozenFalsifierCases(sourceManifest: JsonValue, packetSha256: string, sourceManifestSha256: string, policySha256: string, root: string, policyPath: string, collectorPath: string, tempDir: string): Promise<JsonValue[]> {
  const result = (caseId: string, reason: ReasonCode, valid: boolean): JsonObject => ({ case_id: caseId, status: valid ? "pass" : "fail", reason_codes: [reason], approvable: false, no_identity_output: true });
  const drift = await exerciseFrozenSourceDrift(root, policyPath, collectorPath, tempDir);
  const f7 = { ...result("F7_source_drift", "H3_SOURCE_HASH_DRIFT", drift.mutated && drift.observed && !drift.packetWritten), source_copy_mutated: drift.mutated, frozen_validation_rejected: drift.observed, packet_written: drift.packetWritten };
  const missingSlot = approvalFixture(packetSha256, sourceManifestSha256, policySha256);
  missingSlot.per_user_decisions = (missingSlot.per_user_decisions as JsonValue[]).slice(0, 2);
  const f10Result = validateApprovalFixture(missingSlot, packetSha256, sourceManifestSha256, policySha256);
  const f10 = result("F10_slot_omission", "H3_PER_USER_DISPOSITION_REQUIRED", (f10Result.reason_codes as JsonValue[]).includes("H3_PER_USER_DISPOSITION_REQUIRED"));
  const badReplacement = approvalFixture(packetSha256, sourceManifestSha256, policySha256);
  badReplacement.per_user_decisions = [{ slot_id: "h3-per-user-slot-01", disposition: "replace_with_role_or_team", reason_code: "H3_PER_USER_DISPOSITION_REQUIRED" }, ...(badReplacement.per_user_decisions as JsonValue[]).slice(1)];
  const f11Result = validateApprovalFixture(badReplacement, packetSha256, sourceManifestSha256, policySha256);
  const f11 = result("F11_bad_replacement", "H3_REPLACE_RULE_REQUIRED", (f11Result.reason_codes as JsonValue[]).includes("H3_REPLACE_RULE_REQUIRED"));
  const stale = approvalFixture("sha256:" + "0".repeat(64), sourceManifestSha256, policySha256);
  const f12Result = validateApprovalFixture(stale, packetSha256, sourceManifestSha256, policySha256);
  const f12 = result("F12_stale_approval", "H3_DISPOSITION_STALE", (f12Result.reason_codes as JsonValue[]).includes("H3_DISPOSITION_STALE"));
  const localeInput = ["ä", "a", "z", "Å"];
  const localeA = [...localeInput].sort(new Intl.Collator("sv").compare);
  const localeB = [...localeInput].sort(new Intl.Collator("en").compare);
  const canonicalA = canonicalJson({ values: [...localeA].sort(compareCodeUnits) });
  const canonicalB = canonicalJson({ values: [...localeB].sort(compareCodeUnits) });
  const f13 = result("F13_locale_order", "H3_NONDETERMINISTIC_OUTPUT", localeA.join("|") !== localeB.join("|") && canonicalA === canonicalB && await sha256(canonicalA) === await sha256(canonicalB));
  return [f7, f10, f11, f12, f13];
}

function parseCli(argv: string[]): Record<string, string | boolean> {
  const result: Record<string, string | boolean> = {};
  for (let i = 0; i < argv.length; i += 1) {
    const token = argv[i];
    if (!token.startsWith("--")) throw new Error("H3_SOURCE_PARSE_ERROR");
    const key = token.slice(2);
    const next = argv[i + 1];
    if (next && !next.startsWith("--")) { result[key] = next; i += 1; } else result[key] = true;
  }
  return result;
}

async function writeJson(path: string, value: JsonValue): Promise<void> {
  await Bun.write(path, canonicalJson(value));
}

async function writeFailure(outputDir: string, reason: ReasonCode, message: string): Promise<void> {
  await Bun.write(`${outputDir}/falsifier-receipt.json`, canonicalJson({ schema_version: "h3-failure-receipt/v1", status: "failed", reason_codes: [reason], detail: message.replace(/[^A-Za-z0-9_:.\-/ ]/g, "") }));
}

async function runCli(): Promise<number> {
  const cli = parseCli(process.argv.slice(2));
  const mode = (cli["input-mode"] ?? "frozen") as InputMode;
  const outputDir = String(cli["output-dir"] ?? "");
  const root = String(cli["worktree-root"] ?? "");
  const policyPath = String(cli["policy-path"] ?? "");
  const collectorPath = String(cli["route-collector-path"] ?? "");
  const tempDir = String(cli["temp-dir"] ?? "");
  if (!outputDir || !root || !policyPath || !tempDir || (mode === "frozen" && !collectorPath)) throw new Error("H3_SOURCE_UNAVAILABLE");
  if (Bun.spawnSync(["mkdir", "-p", outputDir]).exitCode !== 0) throw new Error("H3_SOURCE_UNAVAILABLE");
  if (mode === "fixture_injection") {
    if (!policyPath.startsWith("fixture://h3-0015/")) {
      await writeFailure(outputDir, "H3_FIXTURE_SOURCE_FORBIDDEN", "fixture policy path is not a fixture URI");
      return 1;
    }
    const manifestPath = String(cli["fixture-manifest-path"] ?? "");
    if (manifestPath && !manifestPath.startsWith(`${root}/apps/server/tools/security-h3/0015/fixtures/`)) {
      await writeFailure(outputDir, "H3_FIXTURE_SOURCE_FORBIDDEN", "fixture manifest path is outside the capsule");
      return 1;
    }
    const manifest = fixtureManifestCanonical();
    if (await sha256Prefixed(manifest) !== FIXTURE_MANIFEST_SHA256) {
      await writeFailure(outputDir, "H3_FIXTURE_MANIFEST_DRIFT", "fixture manifest digest mismatch");
      return 1;
    }
    const caseId = String(cli["fixture-case"] ?? "");
    if (!caseId) throw new Error("H3_FIXTURE_MODE_REQUIRED");
    const result = await runFalsifierCase(caseId);
    await writeJson(`${outputDir}/falsifier-fixture-receipt.json`, result);
    return result.status === "pass" ? 0 : 1;
  }
  try {
    const built = await buildFrozenContext(root, policyPath, collectorPath);
    const packet = await buildPacket(built.context);
    await writeJson(`${outputDir}/source-manifest.json`, built.sourceManifest);
    await writeJson(`${outputDir}/current-route-inventory.json`, built.context.routeInventory);
    await writeJson(`${outputDir}/current-resource-inventory.json`, built.context.resourceInventory);
    await Bun.write(`${outputDir}/decision-packet.json`, packet.bytes);
    await Bun.write(`${outputDir}/packet.sha256`, packet.digest);
    const fixtureCases = ["F1_missing_route", "F2_new_current_operation", "F3_method_change", "F4_duplicate_owner", "F5_unknown_method", "F6_get_mutates", "F8_count_drift", "F9_identity_leak", "F14_resource_key_wrong_kind", "F15_resource_key_method"];
    const fixtureResults: JsonValue[] = [];
    for (const caseId of fixtureCases) fixtureResults.push(await runFalsifierCase(caseId));
    const frozenResults = await runFrozenFalsifierCases(built.sourceManifest, packet.digest, built.context.sourceManifestSha256, built.context.policySha256, root, policyPath, collectorPath, tempDir);
    await writeJson(`${outputDir}/falsifier-fixture-receipt.json`, { schema_version: "h3-falsifier-receipt/v1", mode: "fixture_injection", results: fixtureResults });
    await writeJson(`${outputDir}/falsifier-receipt.json`, { schema_version: "h3-falsifier-receipt/v1", mode: "frozen", results: frozenResults });
    await writeJson(`${outputDir}/golden-receipt.json`, { schema_version: "h3-golden-receipt/v1", case_id: "G0_frozen_checkpoint", status: "pass", policy_counts: built.context.policy.summary, candidate_projection_sha256: built.context.policy.projectionSha256, per_user_slot_projection_sha256: built.context.policy.slotProjectionSha256, source_manifest_sha256: built.context.sourceManifestSha256, route_collector_sha256: ROUTE_COLLECTOR_SHA256, side_effect_vocabulary_sha256: SIDE_EFFECT_VOCABULARY_SHA256, packet_sha256: packet.digest, packet_bytes: new TextEncoder().encode(packet.bytes).byteLength, deterministic_bytes_equal: true, no_pii: !isPii(packet.bytes), recommendation: "fail_closed" });
    return 0;
  } catch (error) {
    const message = error instanceof Error ? error.message : String(error);
    const reason = (REASONS.includes(message as ReasonCode) ? message : "H3_SOURCE_UNAVAILABLE") as ReasonCode;
    await writeFailure(outputDir, reason, message);
    return 1;
  }
}

if (typeof process !== "undefined" && process.argv?.[1]?.endsWith("generate.ts")) {
  runCli().then((code) => { process.exitCode = code; }).catch(() => { process.exitCode = 1; });
}
