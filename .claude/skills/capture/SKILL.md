---
name: capture
description: Capture session insights and log decisions. Use when "capture insights", "log decisions", "session summary", "what did we learn", "check context", or "document decisions".
allowed-tools: Read, Edit, Write, Glob, Grep
argument-hint: [quick]
user-invocable: true
---

# Capture

Scans conversation context for insights worth persisting. Routes each finding to the right file. Interactive — presents findings for user confirmation before writing.

## Scan Categories

| Category | What to look for | Destination |
|----------|-----------------|-------------|
| Architecture decision | Design choices with rationale | `.planning/STATE.md` decisions section |
| Error → fix pattern | Non-obvious error resolution | `docs/troubleshooting.md` |
| Code convention | Pattern established or confirmed | `docs/conventions.md` |
| Workflow/tool insight | What worked or didn't | `docs/conventions.md` or relevant doc |
| Task completion | Progress, new blockers | `.planning/STATE.md` progress section |

## Workflow

### 1. Scan Context

Review the conversation for items matching the scan categories above.

**Worth capturing if:**
- Took >1 attempt to solve (non-obvious)
- Would affect future development decisions
- Is a pattern/convention not yet documented
- Is an error message with a non-obvious fix

**NOT worth capturing if:**
- One-off issue unlikely to recur
- Already documented in existing docs (check first)
- Too specific to be reusable

### 2. Check Existing Docs

Before presenting findings, read only the destination files relevant to findings from step 1. Don't read all files unconditionally.

For example, if step 1 found only a gotcha and a convention, read only `docs/troubleshooting.md` and `docs/conventions.md`.

Find MEMORY.md by searching `~/.claude/projects/*/memory/MEMORY.md` matching the current project, or use the path from project instructions context.

Skip any finding that's already documented.

### 3. Present Findings

If **no insights found**: Report "No new insights found in this conversation." and stop.

If insights found, present as a numbered checklist:

```
## Captured Insights

1. [Architecture] → .planning/STATE.md
   Draft: <concise text>

2. [Convention] → docs/conventions.md
   Draft: <concise text>

3. [Gotcha] → docs/troubleshooting.md
   Draft: <concise text>
```

Ask: **"Which items should I write? (all / numbers to keep / numbers to skip)"**

### 4. Apply

Write only confirmed items directly to their destination files:
- Read the target file
- Find the appropriate section (match by topic)
- Append in the file's existing format
- If no clear section exists, append under a relevant heading

All confirmed items write directly — no staging. The user confirmation gate replaces the need for a staging step.

## Arguments

- No args → full scan and present
- `quick` → scan but only report counts per category, don't draft text unless user asks

## Rules

- Never write without user confirmation
- Never duplicate: always check destination before appending
- Preserve target doc formatting and section structure
- One fact per entry — concise over detailed
- Do NOT trigger on "sync knowledge" or "promote memory" (those belong to /knowledge-sync)
