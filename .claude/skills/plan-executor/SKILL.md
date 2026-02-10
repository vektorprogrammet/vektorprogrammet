---
name: plan-executor
description: Execute plans in fresh contexts, git commit per task, auto-verify. Use when "execute plan", "run this", "implement phase", or references PLAN.md.
user-invocable: false
allowed-tools: Read, Edit, Write, Grep, Glob, Bash(*)
argument-hint: [mode:interactive|yolo]
---

# Plan Executor

Fresh agent per task = no degradation.

```
Main (30%) → Agent 1 (fresh 200k) → Agent 2 (fresh 200k)
```

## Workflow

Load plan from `.planning/`, extract tasks, waves, and verification criteria.

### Execution

For each wave in the plan:

**Sequential tasks**: Use the Task tool to spawn one agent at a time. Wait for completion, verify, commit, then spawn next.

**Parallel tasks**: Spawn all agents in the wave simultaneously using multiple Task tool calls. Wait for all, verify all, commit all.

### Agent Context

Give each agent only what it needs:
- Task ID and description
- Files to create/modify
- Action steps
- Verification criteria
- What the previous task accomplished (if sequential)

Exclude: history, unrelated tasks, full project context.

### Verification

After each task, run the project's build/lint/test commands. Compare test counts to baseline in `.planning/test-baseline.md` if it exists.

### Git Commit

One commit per task:

```
Task <id>: <description>

Verified: <build/lint/test results>
Plan: .planning/phases/<phase>/<plan>.md
```

### Update STATE.md

After each task, append completion status to STATE.md with commit hash and next step.

## Modes

Pass mode as argument: `/plan-executor interactive` or `/plan-executor yolo`

- **interactive** (default): Ask user before each task
- **yolo**: Auto-commit, no prompts between tasks

## Stopping

1. All tasks complete
2. Verification fails 3 times on same task → stop, report
3. Context limit approaching → commit current, write state, stop
4. Max 10 iterations without progress

## Error Recovery

On failure: analyze error, spawn fresh agent with error context, retry.
After 3 failures on same error: STOP and report to user.
