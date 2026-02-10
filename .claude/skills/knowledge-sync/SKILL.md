---
name: knowledge-sync
description: Promote staged insights to docs or scan conversation for new insights. Use when "sync knowledge", "promote memory", end of session, or triggered by Stop hook.
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

## Mode: Scan

Analyze current conversation for insights worth capturing. Heavier, uses context.

### Steps

1. **Review conversation** for:
   - Errors encountered and how they were fixed
   - Workarounds or non-obvious solutions
   - New conventions established or discovered
   - Decisions about patterns or architecture
   - Gotchas that cost time
2. **Check existing docs**: Read target docs to avoid duplicating knowledge already captured.
3. **Write staging entries**: Add new entries to MEMORY.md staging section with `→ target:` tags.
4. **Report**: List what was staged and suggested targets. Let user confirm before promoting.

### Scan Criteria

Worth staging if:
- Took >1 attempt to solve (non-obvious)
- Would affect future development decisions
- Is a pattern/convention not yet documented
- Is an error message with a non-obvious fix

NOT worth staging if:
- One-off issue unlikely to recur
- Already documented in existing docs
- Too project-specific to be reusable

## Arguments

- No args or `promote` → promote mode
- `scan` → scan mode
- `scan promote` → scan then immediately promote

## Rules

- Never duplicate: check target doc before appending
- Preserve target doc formatting and section structure
- One fact per bullet point — concise over detailed
- Delete stale entries aggressively
- When triggered by Stop hook: promote entries and let session end. Don't scan for new insights — context may be exhausted.
