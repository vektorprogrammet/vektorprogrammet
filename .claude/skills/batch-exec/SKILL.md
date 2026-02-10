---
name: batch-exec
description: Parallel execution of independent tasks via subagents. Use when multiple unrelated changes needed, "do all of these", or list of tasks.
user-invocable: false
allowed-tools: Read, Edit, Write, Grep, Glob, Bash(*)
---

# Batch Exec

N independent tasks → N parallel agents → verify all → commit all.

## Workflow

### 1. Parse Tasks

From user input, extract independent work items. Each needs:
- Clear description
- Target files (no overlap)
- Verification criteria

### 2. Independence Check

Tasks MUST NOT share files. If overlap detected:
- Split into waves (sequential groups)
- Or merge into single rapid-task

Example:
```
Tasks: [A: edit foo.ts, B: edit bar.ts, C: edit foo.ts]
→ Wave 1: [A, B] parallel  (no overlap)
→ Wave 2: [C] sequential   (depends on A)
```

### 3. Dispatch

Use the Task tool to spawn each task as a subagent. Each agent gets:
- Task description
- Target files
- Verification criteria
- Instructions to implement, verify, and report back

For each wave:
1. Spawn all agents in the wave in parallel using multiple Task tool calls
2. Wait for all to complete
3. If all pass, commit each task's changes
4. If any fail, report failures before continuing to next wave

### 4. Report

```markdown
## Batch Results

- [x] Task 1: signup validation (commit a3f89d2)
- [x] Task 2: date parsing fix (commit b4e12c3)
- [ ] Task 3: button styles (lint failed, needs review)
```

## Limits

- Max 6 parallel agents (resource constraint)
- Each task <5 files
- If any task is ambiguous → exclude from batch, flag for user
