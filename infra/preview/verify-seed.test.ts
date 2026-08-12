import { mkdtemp, readFile, rm } from "node:fs/promises";
import { tmpdir } from "node:os";
import { join } from "node:path";
import { afterEach, expect, test } from "bun:test";
import { generateSeed, loadPolicy, writeSeed } from "./generate-seed";
import { verifySeed } from "./verify-seed";

const temporaryDirectories: string[] = [];

afterEach(async () => {
  await Promise.all(temporaryDirectories.splice(0).map((directory) => rm(directory, { recursive: true, force: true })));
});

test("accepts deterministic synthetic SQL whose schema includes password_reset", async () => {
  const policy = await loadPolicy();
  const first = generateSeed(policy);
  const second = generateSeed(policy);
  expect(second.artifactSha256).toBe(first.artifactSha256);
  expect(second.manifestSha256).toBe(first.manifestSha256);
  expect(first.manifest.tableNames).toContain("password_reset");

  const outputDirectory = await mkdtemp(join(tmpdir(), "vektor-preview-seed-test-"));
  temporaryDirectories.push(outputDirectory);
  await writeSeed(outputDirectory);
  await expect(verifySeed(outputDirectory)).resolves.toMatchObject({
    tableCount: 65,
    totalRowCount: 45955,
    deterministicReplay: true,
    forbiddenFindings: 0,
  });
  expect(await readFile(join(outputDirectory, "seed.sql"), "utf8")).toContain("`password_reset`");
});
