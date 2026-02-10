---
name: knowledge-sync
description: Promote staged MEMORY.md insights to docs. Use when "sync knowledge", "promote memory", end of session, or triggered by Stop hook.
allowed-tools: Read, Edit, Write, Grep, Glob
user-invocable: true
---

# Knowledge Sync

Insights flow: MEMORY.md staging → `docs/`. Two modes.

## Mode: Promote (default)

Flush staging to public docs. Fast, no analysis.

### Steps

1. **Find MEMORY.md**: Search for it in `~/.claude/projects/*/memory/MEMORY.md` matching the current project, or use the path from project instructions context.
2. **Read MEMORY.md**: Find section starting with `## Staging`. If it contains `_Empty` or has no bullet entries, report "Nothing to promote" and stop.
3. **Parse entries**: Each staged entry follows:
   ```
   - <insight> → target: docs/<file>.md
   ```
4. **For each entry**:
   - Read the target doc. If it doesn't exist, create it with `# <Title>` header.
   - Find the appropriate section (match by topic)
   - Append the insight in the doc's existing format
   - If no clear section, append under a new `## Other` section
5. **Update MEMORY.md**: Replace staging entries with:
   ```
   _Empty — all insights promoted to `docs/conventions.md` and `docs/troubleshooting.md`._
   ```
6. **Report**: List what was promoted and where.

### Target Docs

| Insight type | Target |
|---|---|
| Error → fix | `docs/troubleshooting.md` |
| Convention / pattern | `docs/conventions.md` |
| Architecture insight | `docs/architecture.md` |
| Test knowledge | `docs/testing.md` |

## Arguments

- No args or `promote` → promote mode

## Rules

- Never duplicate: check target doc before appending
- Preserve target doc formatting and section structure
- One fact per bullet point — concise over detailed
- Delete stale entries aggressively
- When triggered by Stop hook: promote entries and let session end. Don't scan for new insights — context may be exhausted.
