---
name: review
description: Code quality review. Checks diff for logic issues, convention violations, security concerns. Use when "review this", "check my code", or before committing. Optional quality gate.
allowed-tools: Read, Glob, Grep, Bash(git diff:*), Bash(git log:*)
user-invocable: true
argument-hint: [scope]
---

# Review

Lightweight LLM-as-judge code review. Read-only — reports findings but does not fix code.

## Workflow

### 1. Get the Changeset

- Run `git diff --staged` first
- If nothing staged, fall back to `git diff`
- If arg provided (e.g., `/review HEAD~3`), use `git diff <scope>`
- For branch review: `git diff master...HEAD`

### 2. Load Conventions

Read `docs/conventions.md` for project-specific standards.

### 3. Review Against Checklist

Analyze every changed hunk against:

| Category | What to check |
|----------|--------------|
| **Convention** | Does it follow patterns in conventions.md? Naming, structure, DI patterns? |
| **Logic** | Obvious bugs, off-by-one, null/empty checks, edge cases? |
| **Security** | Injection (SQL, XSS, command), unsafe operations, exposed secrets? |
| **Naming** | Clear, consistent with surrounding code? |
| **Dead code** | Unused imports, unreachable branches, leftover debug code? |

### 4. Present Findings

```
## Review: <scope description>

### Issues
- [CONVENTION] path/to/file:42 — description of violation
- [LOGIC] path/to/file:78 — description of bug risk
- [SECURITY] path/to/file:15 — description of concern

### Suggestions (optional improvements, not blocking)
- path/to/file:30 — description of improvement

### Verdict: APPROVE | COMMENT | REQUEST_CHANGES
```

- **APPROVE**: No issues found. "Ready to commit."
- **COMMENT**: Only suggestions, no blockers. "Ready to commit, consider suggestions."
- **REQUEST_CHANGES**: Issues found that should be fixed before committing.

If REQUEST_CHANGES: present issues and ask user how to proceed.

## Constraints

- Read-only. Do NOT fix code — only report.
- Do NOT run tests — that's the verify agent's job.
- Do NOT commit anything.
- Focus on the diff, not the entire file (unless context is needed to understand a change).
- Be specific: always include file:line references.
- Be concise: one sentence per finding.
- Don't nitpick style if conventions.md doesn't mention it.
