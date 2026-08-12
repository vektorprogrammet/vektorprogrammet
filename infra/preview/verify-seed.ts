import { createHash } from "node:crypto";
import { readFile } from "node:fs/promises";
import { resolve } from "node:path";
const forbidden = /vektorprogrammet\.no|password|secret|token|@|\b[A-Z][a-z]+\s[A-Z][a-z]+/i;
const hash = (value: string) => createHash("sha256").update(value).digest("hex");
export async function verifySeed(seedDirectory: string, policyPath = new URL("./seed-policy.json", import.meta.url)): Promise<void> {
  const policy = JSON.parse(await readFile(policyPath, "utf8")) as { tableCount: number; totalRowCount: number; app: string; stage: string };
  const sqlPath = resolve(seedDirectory, "synthetic-seed.sql");
  const manifestPath = resolve(seedDirectory, "synthetic-seed.manifest.json");
  const sql = await readFile(sqlPath, "utf8");
  const manifest = JSON.parse(await readFile(manifestPath, "utf8")) as { tableCount: number; totalRowCount: number; digest: string };
  if (forbidden.test(sql) || forbidden.test(JSON.stringify(manifest))) throw new Error("Forbidden value in synthetic artifact");
  const tableCount = (sql.match(/CREATE TABLE IF NOT EXISTS/g) ?? []).length;
  const rowCount = (sql.match(/INSERT INTO/g) ?? []).length;
  if (tableCount !== policy.tableCount || rowCount !== policy.totalRowCount) throw new Error("Synthetic table/row count mismatch");
  if (manifest.tableCount !== policy.tableCount || manifest.totalRowCount !== policy.totalRowCount || manifest.digest !== `sha256:${hash(sql)}`) throw new Error("Synthetic manifest mismatch");
}
if (process.argv[1] && resolve(process.argv[1]) === resolve(new URL(import.meta.url).pathname)) verifySeed(process.argv[2] ?? ".preview-seed").then(() => console.log("synthetic seed verified")).catch((error) => { console.error(error); process.exitCode = 1; });
