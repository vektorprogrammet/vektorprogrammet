# Knowledge Workflow

How project knowledge flows from discovery to documentation.

## The Problem

Working on a codebase produces insights — error patterns, conventions, gotchas. Without a system, these stay in one person's (or one AI session's) head and get rediscovered repeatedly.

## Architecture

Knowledge lives in three tiers:

```
Tier 1: Staging (private, ephemeral)
  MEMORY.md — unvalidated observations from current work
  ↓ confirmed by testing/usage
Tier 2: Project docs (public, durable)
  docs/conventions.md — how we do things
  docs/troubleshooting.md — error → fix lookup
  docs/architecture.md, testing.md, etc.
  ↓ summarized for quick loading
Tier 3: Entry points (public, concise)
  CLAUDE.md — agent workflow (loaded every prompt)
  README.md — human setup
```

## Promotion Flow

```
Encounter insight
  → Write to MEMORY.md staging section
  → Tag with target: docs/troubleshooting.md, docs/conventions.md, etc.

End of session
  → Review staging entries
  → Confirmed useful? → Append to target doc, delete from staging
  → Stale or wrong? → Delete from staging
  → Agent-workflow-specific? → Update CLAUDE.md instead
```

## What Goes Where

| Insight type | Target |
|---|---|
| Error message → fix | `docs/troubleshooting.md` |
| "Always do X" / "Never do Y" | `docs/conventions.md` |
| How a subsystem works | `docs/architecture.md` |
| Test commands / workflow | `docs/testing.md` |
| Agent-only behavior (sandbox, skills) | `CLAUDE.md` |
| Unvalidated / needs confirmation | `MEMORY.md` staging |

## Rules

1. **Stage before committing to docs** — write new insights to MEMORY.md first. Promote after confirming they're correct and reusable.
2. **No duplication** — each fact lives in one place. CLAUDE.md references docs/ via the table, doesn't repeat content.
3. **Concise over complete** — docs are loaded into AI context. Every line costs tokens. If a fact isn't worth re-reading every session, it doesn't belong in CLAUDE.md.
4. **Public by default** — technical knowledge goes in `docs/` where all contributors benefit. Only agent-specific behavior (sandbox workarounds, skill routing) stays private.
5. **Delete aggressively** — outdated entries are worse than missing ones. When conventions change, update the doc immediately.

## Example

During a session, you discover that `bin/console cache:clear` fails silently when the cache dir has wrong permissions:

```markdown
## Staging (MEMORY.md)
- cache:clear fails silently with wrong permissions → target: docs/troubleshooting.md
```

At end of session, after confirming the issue is real and reproducible:

```markdown
## Environment (docs/troubleshooting.md)
**cache:clear fails silently with wrong permissions**:
→ Check `var/cache/` ownership. Fix: `chmod -R u+w var/cache/`
```

Then delete the staging entry from MEMORY.md.

## Automation

The workflow is automated via a skill and a hook:

### `/knowledge-sync` skill (promote only)

- **`/knowledge-sync`**: reads MEMORY.md staging, appends confirmed entries to target docs, clears staging. Fast, no analysis.

For conversation scanning, use `/capture` instead — it interactively presents findings for user confirmation and writes directly to docs.

### Stop hook

`.claude/hooks/knowledge-sync-reminder.sh` fires when the agent is about to end a session:
1. Derives MEMORY.md path from project directory
2. Checks if staging section has entries (not just the `_Empty` placeholder)
3. If entries exist: blocks the stop with a reminder to run `/knowledge-sync`
4. If staging is empty: no-op, agent stops normally
5. Uses `stop_hook_active` flag to prevent infinite loops — only fires once

**Protocol notes**: Stop hooks read event data from stdin (not `$CLAUDE_HOOK_EVENT_DATA`). Return `{"decision": "block", "reason": "..."}` to prevent stopping. The `stop_hook_active` boolean in the input is `true` when the hook has already fired once — always check this to prevent infinite loops.

### Full loop

```
Agent works on tasks
  → Encounters insight
  → /capture scans conversation, presents findings, writes to docs with user confirmation
  → Or: writes to MEMORY.md staging for later batch promotion

Agent finishes and tries to stop
  → Stop hook checks staging
  → If non-empty: blocks stop, reminds agent to promote
  → Agent runs /knowledge-sync → entries promoted to docs/
  → Agent stops (hook sees empty staging, allows stop)

Next session
  → New developer (human or AI) reads docs/
  → Benefits from previous session's insights
```
