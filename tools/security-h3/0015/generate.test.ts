import { expect, test } from "bun:test";
import {
  CANDIDATE_ORDINALS,
  CANDIDATE_PROJECTION_SHA256,
  FIXTURE_MANIFEST_SHA256,
  SOURCE_MANIFEST_SHA256,
  SLOT_PROJECTION_SHA256,
  canonicalJson,
  runFalsifierCase,
  sha256,
  validateApprovalFixture,
  validateSchemaForTest,
} from "./generate.ts";

const root = decodeURIComponent(new URL("../../../../../", import.meta.url).pathname).replace(/\/$/, "");
const generator = `${root}/apps/server/tools/security-h3/0015/generate.ts`;
const policy = "/srv/share/projects/vektorprogrammet/docs/live-access-policy-2026-08-10.md";
const pid = typeof process !== "undefined" ? process.pid : 0;

function remove(path: string): void {
  Bun.spawnSync(["rm", "-rf", path]);
}

const collector = `/tmp/security-h3-0015-test-${pid}-collector.json`;
const pinnedCollector = `${root}/evidence/security-h3/0015/route-collector.json`;

async function ensureCollector(): Promise<void> {
  if (Bun.spawnSync(["test", "-s", collector]).exitCode === 0) return;
  if (Bun.spawnSync(["test", "-s", pinnedCollector]).exitCode !== 0) throw new Error(`missing pinned route collector: ${pinnedCollector}`);
  await Bun.write(collector, await Bun.file(pinnedCollector).arrayBuffer());
}

async function runFrozen(output: string, temp: string, locale = "C"): Promise<void> {
  await ensureCollector();
  const result = Bun.spawnSync([process.execPath, generator, "--input-mode", "frozen", "--policy-path", policy, "--worktree-root", root, "--route-collector-path", collector, "--output-dir", output, "--temp-dir", temp], { stdout: "pipe", stderr: "pipe", env: { PATH: Bun.env.PATH ?? "/usr/bin:/bin", LC_ALL: locale, LANG: locale } });
  expect(result.exitCode).toBe(0);
}

test("pinned fixture manifest and projection vocabulary are stable", async () => {
  const manifest = await Bun.file(`${root}/apps/server/tools/security-h3/0015/fixtures/falsifier-manifest.json`).text();
  expect(`sha256:${await sha256(manifest)}`).toBe(FIXTURE_MANIFEST_SHA256);
  expect(CANDIDATE_PROJECTION_SHA256).toBe("sha256:7c0b235011ec0e1473a40219ff1f248b016c5aa073c851b0fdda5dc6d2c165a3");
  expect(SLOT_PROJECTION_SHA256).toBe("sha256:6391905e31dbc3e4e6c7b195d5ab54f45ce3ca06a0961ada00cca35e2e61a5ba");
});

test("every semantic falsifier fails closed without an approvable packet", async () => {
  const cases = [
    ["F1_missing_route", "H3_LEGACY_CANDIDATE_MISSING_ROUTE"],
    ["F2_new_current_operation", "H3_CURRENT_OPERATION_UNSEEN_IN_POLICY"],
    ["F3_method_change", "H3_METHOD_MISMATCH"],
    ["F4_duplicate_owner", "H3_DUPLICATE_OPERATION"],
    ["F5_unknown_method", "H3_METHOD_UNRESOLVED"],
    ["F6_get_mutates", "H3_GET_SIDE_EFFECT"],
    ["F8_count_drift", "H3_POLICY_COUNT_MISMATCH"],
    ["F9_identity_leak", "H3_PII_INPUT"],
    ["F14_resource_key_wrong_kind", "H3_KEY_KIND_MISMATCH"],
    ["F15_resource_key_method", "H3_KEY_KIND_MISMATCH"],
  ] as const;
  for (const [caseId, reason] of cases) {
    const result = await runFalsifierCase(caseId);
    expect(result.status).toBe("pass");
    expect(result.approvable).toBe(false);
    expect(result.reason_codes).toContain(reason);
    expect(result.no_identity_output).toBe(true);
  }
});

test("local schema validator rejects values above maximum", () => {
  const bounded = { type: "integer", minimum: 0, maximum: 3 };
  expect(validateSchemaForTest(3, bounded)).toEqual([]);
  expect(validateSchemaForTest(4, bounded)).toContain("$:maximum");
});

test("frozen generation is byte deterministic and retains fixed census", async () => {
  const first = `/tmp/security-h3-0015-test-${pid}-a`;
  const second = `/tmp/security-h3-0015-test-${pid}-b`;
  remove(first);
  remove(second);
  await runFrozen(first, "/tmp/security-h3-0015-temp-a", "C");
  await runFrozen(second, "/tmp/security-h3-0015-temp-b", "tr_TR.UTF-8");
  const firstPacket = await Bun.file(`${first}/decision-packet.json`).text();
  const secondPacket = await Bun.file(`${second}/decision-packet.json`).text();
  expect(firstPacket).toBe(secondPacket);
  const firstBytes = new Uint8Array(await Bun.file(`${first}/decision-packet.json`).arrayBuffer());
  const secondBytes = new Uint8Array(await Bun.file(`${second}/decision-packet.json`).arrayBuffer());
  expect(firstBytes.length).toBe(secondBytes.length);
  expect(await sha256(firstBytes)).toBe(await sha256(secondBytes));
  const golden = JSON.parse(await Bun.file(`${first}/golden-receipt.json`).text()) as { packet_bytes: number };
  expect(golden.packet_bytes).toBe(firstBytes.byteLength);
  expect([...firstBytes]).toEqual([...secondBytes]);
  const resources = JSON.parse(await Bun.file(`${first}/current-resource-inventory.json`).text()) as Array<Record<string, unknown>>;
  const routes = JSON.parse(await Bun.file(`${first}/current-route-inventory.json`).text()) as Array<Record<string, unknown>>;
  expect(resources.some((row) => String(row.controller_or_resource_ref).endsWith("\\Article"))).toBe(true);
  expect(resources.some((row) => String(row.controller_or_resource_ref).endsWith("\\FieldOfStudy"))).toBe(true);
  expect(routes.some((row) => /Liip|Elfinder/.test(String(row.owner_ref)) && Array.isArray(row.sideEffectClasses) && row.sideEffectClasses.includes("unknown"))).toBe(true);
  const packet = JSON.parse(firstPacket) as { status: string; policy_rows: unknown[]; legacy_public_candidates: unknown[]; per_user_slots: unknown[]; current_operations: unknown[]; source: { source_manifest_sha256: string } };
  expect(packet.status).toBe("generated");
  expect(packet.policy_rows).toHaveLength(229);
  expect(packet.legacy_public_candidates).toHaveLength(62);
  expect(packet.per_user_slots).toHaveLength(3);
  expect(packet.current_operations.length).toBeGreaterThan(0);
  expect(packet.source.source_manifest_sha256).toBe(SOURCE_MANIFEST_SHA256);
  expect(firstPacket).not.toContain("Jan Haakon");
  expect(firstPacket).not.toContain("Youlduz");
  remove(first);
  remove(second);
});
test("loads exact closed schema, golden receipt, and reason catalog", async () => {
  const schema = JSON.parse(await Bun.file(`${root}/apps/server/tools/security-h3/0015/schema.json`).text()) as Record<string, unknown>;
  const golden = JSON.parse(await Bun.file(`${root}/evidence/security-h3/0015/golden-receipt.json`).text()) as Record<string, unknown>;
  const reasons = JSON.parse(await Bun.file(`${root}/apps/server/tools/security-h3/0015/reason-codes.json`).text()) as { schema_version: string; reason_codes: Array<{ code: string }> };
  expect(schema.$id).toBe("h3-decision-packet/v1");
  expect(schema.additionalProperties).toBe(false);
  expect(golden.schema_version).toBe("h3-golden-receipt/v1");
  expect(golden.status).toBe("pass");
  expect(golden.recommendation).toBe("fail_closed");
  expect(reasons.schema_version).toBe("h3-reason-codes/v1");
  expect(reasons.reason_codes).toHaveLength(31);
  expect(reasons.reason_codes.map((item) => item.code)).toContain("H3_SOURCE_PARSE_ERROR");
});

function approvalFixtureForTest(): Parameters<typeof validateApprovalFixture>[0] {
  return {
    schema_version: "h3-operator-disposition/v1",
    approval_id: "op-test-0015",
    approval_artifact_ref: "fixture://h3-0015/operator",
    packet_sha256: "sha256:" + "1".repeat(64),
    source_manifest_sha256: SOURCE_MANIFEST_SHA256,
    policy_sha256: "sha256:" + "2".repeat(64),
    operator_ref: "operator:test-0015",
    environment: "test",
    public_decisions: CANDIDATE_ORDINALS.map((ordinal) => ({ candidate_id: `policy-row-${String(ordinal).padStart(3, "0")}`, decision: "deny", reason_code: "H3_DEFAULT_DENY" })),
    per_user_decisions: Array.from({ length: 3 }, (_, index) => ({ slot_id: `h3-per-user-slot-0${index + 1}`, disposition: "remove", reason_code: "H3_PER_USER_DISPOSITION_REQUIRED", removal_date: "2099-01-01", effective_at: "2099-01-01T00:00:00Z" })),
    unresolved_acknowledged: [],
    rollback_ref: { ref: "fixture://h3-0015/rollback", owner_ref: "operator:test-0015" },
  };
}

test("operator disposition validation is closed over IDs and conditions", () => {
  const packetSha256 = "sha256:" + "1".repeat(64);
  const policySha256 = "sha256:" + "2".repeat(64);
  const valid = validateApprovalFixture(approvalFixtureForTest(), packetSha256, SOURCE_MANIFEST_SHA256, policySha256);
  expect(valid.valid).toBe(true);
  expect(valid.approvable).toBe(false);
  const nonCandidate = approvalFixtureForTest();
  nonCandidate.public_decisions = [{ candidate_id: "policy-row-003", decision: "deny", reason_code: "H3_DEFAULT_DENY" }, ...(nonCandidate.public_decisions as Array<Record<string, unknown>>).slice(1)];
  expect(validateApprovalFixture(nonCandidate, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_PUBLIC_APPROVAL_REQUIRED");
  const extra = { ...approvalFixtureForTest(), unexpected: true } as Parameters<typeof validateApprovalFixture>[0];
  expect(validateApprovalFixture(extra, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_SOURCE_PARSE_ERROR");
  const duplicateId = approvalFixtureForTest();
  const duplicateEntries = [...(duplicateId.public_decisions as Array<Record<string, unknown>>)];
  duplicateEntries[1] = duplicateEntries[0];
  duplicateId.public_decisions = duplicateEntries;
  expect(validateApprovalFixture(duplicateId, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_PUBLIC_APPROVAL_REQUIRED");
  const wrongKey = approvalFixtureForTest();
  wrongKey.public_decisions = [{ candidate_id: "policy-row-001", decision: "approve_public", reason_code: "H3_DEFAULT_DENY", exact_policy_key: { policyKeyKind: "routing", method: "GET", pathTemplate: "/x", routeName: "wrong" }, response_boundary: "json", effective_at: "2099-01-01T00:00:00Z", review_by: "2099-01-02T00:00:00Z" }, ...(wrongKey.public_decisions as Array<Record<string, unknown>>).slice(1)];
  expect(validateApprovalFixture(wrongKey, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_PUBLIC_APPROVAL_REQUIRED");
  const retainMissingOwner = approvalFixtureForTest();
  retainMissingOwner.per_user_decisions = [{ slot_id: "h3-per-user-slot-01", disposition: "retain_with_owner", reason_code: "H3_DEFAULT_DENY" }, ...(retainMissingOwner.per_user_decisions as Array<Record<string, unknown>>).slice(1)];
  expect(validateApprovalFixture(retainMissingOwner, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_RETAIN_OWNER_REQUIRED");
  const replaceMissingRule = approvalFixtureForTest();
  replaceMissingRule.per_user_decisions = [{ slot_id: "h3-per-user-slot-01", disposition: "replace_with_role_or_team", reason_code: "H3_DEFAULT_DENY", effective_at: "2099-01-01T00:00:00Z" }, ...(replaceMissingRule.per_user_decisions as Array<Record<string, unknown>>).slice(1)];
  expect(validateApprovalFixture(replaceMissingRule, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_REPLACE_RULE_REQUIRED");
  const removeMissingDate = approvalFixtureForTest();
  removeMissingDate.per_user_decisions = [{ slot_id: "h3-per-user-slot-01", disposition: "remove", reason_code: "H3_DEFAULT_DENY" }, ...(removeMissingDate.per_user_decisions as Array<Record<string, unknown>>).slice(1)];
  expect(validateApprovalFixture(removeMissingDate, packetSha256, SOURCE_MANIFEST_SHA256, policySha256).reason_codes).toContain("H3_REMOVE_DATE_REQUIRED");
  const stale = validateApprovalFixture(approvalFixtureForTest(), "sha256:" + "3".repeat(64), SOURCE_MANIFEST_SHA256, policySha256);
  expect(stale.reason_codes).toContain("H3_DISPOSITION_STALE");
  expect(canonicalJson(stale)).not.toContain("approve_public");
});
