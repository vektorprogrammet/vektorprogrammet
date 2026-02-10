---
name: rapid-task
description: Execute concrete task without planning docs. Read, implement, verify, commit. Use when task is clear and 2-5 files.
user-invocable: false
allowed-tools: Read, Edit, Write, Grep, Glob, Bash(*)
---

# Rapid Task

Do the thing. No PLAN.md, no ROADMAP.md. For tasks where the "what" is clear.

## Workflow

### 1. Understand (parallel reads)

Spawn Explore agent to find relevant files. Read all targets simultaneously.
Identify: what changes, what breaks, what tests exist.

### 2. Implement

Change files. Prefer Edit over Write. Minimal diff.

If >5 files or scope creep detected, STOP and suggest `/fresh-context-planner`.

### 3. Verify

Run the project's build, lint, and test commands. Collect exit codes and output.

Auto-detect project commands:
- `composer.json` → composer scripts (test, lint, analyse)
- `package.json` → npm/pnpm scripts
- `Makefile` → make targets
- `Cargo.toml` → cargo check/test
- `pyproject.toml` → pytest

Levels:
- Quick: build + lint (default)
- Standard: build + lint + affected tests
- Full: build + lint + all tests (on request)

### 4. Commit

One commit per task. Message format:

```
<type>: <what changed>

<why, if not obvious>
```

Types: fix, feat, refactor, test, docs, chore

### Scope Guard

If during implementation:
- Touch >5 files → warn user
- Find unrelated bugs → note, don't fix
- Need architectural decision → stop, ask

## Context

Give subagents only:
- Files being changed
- Immediate dependencies (imports)
- Test files for changed code
- Error output if fixing a bug
