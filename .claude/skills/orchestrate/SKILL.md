---
name: orchestrate
description: Session orchestrator. Routes tasks to agents/skills, sequences work, stays lean. Use at session start, "continue", "what's next", or when switching between tasks.
allowed-tools: Read, Glob, Grep, Bash(*), Task(coding, verify, state-sync, execute-plan, web)
---

# Orchestrate

Lean router for the main context. Delegates all heavy work to agents. Stay under 20% context.

## Session Start

1. Read `.planning/STATE.md`
2. Run `git status` and `git log --oneline -5`
3. Run `composer test` — compare results to `.planning/test-baseline.md`
4. Present status summary to user:
   - What was completed last session
   - Current branch, recent commits, uncommitted changes
   - Test health vs baseline (green / regression)
   - Next items from STATE.md
5. **Wait for user confirmation** before doing any work

If tests regress from baseline, that becomes top priority — flag it immediately.

## Routing Table

| Signal | Action |
|--------|--------|
| Single-file trivial fix (<20 lines) | Implement directly in main context |
| Clear 2-5 file task | Spawn **coding** agent |
| Execute PLAN.md | Sequence **execute-plan** agents per wave |
| Explore code | Spawn **Explore** agent (built-in) |
| Verify changes | Spawn **verify** agent |
| Sync state/knowledge | Spawn **state-sync** agent |
| "test this", "run tests" | Invoke `/test` skill |
| "review this", "check code" | Invoke `/review` skill |
| "plan", vague/big scope | Invoke `/plan` skill |
| "ready for PR", "ship" | Invoke `/pr-prep` skill |
| Web research/doc lookup          | Spawn **web** agent with query                 |
| Multiple independent tasks | Spawn parallel **coding** agents (max 3) → parallel verify → full verify |

## Quick Fix Path

For trivial changes (one file, <20 lines, obvious fix like typo/import/one-liner):
- Skip agent delegation — implement directly in main context
- Run `composer test -- --filter=RelevantTest` to verify
- Commit directly
- This avoids the overhead of 3 agent spawns for a one-line change

## Sequencing Pattern

Agents can't spawn agents. The orchestrator sequences all delegation:

```
orchestrate (main) -> coding agent -> [returns] -> verify agent -> [returns] -> state-sync agent -> next task
```

### Standard Task Flow

1. **Coding agent**: implement the change, return summary of what changed
2. **Verify agent**: run tests/lint, compare baseline, return pass/fail report
3. On verify pass: commit the changes with a descriptive message
4. **State-sync agent**: update STATE.md
5. On verify fail: spawn new coding agent with error context (max 2 retries)
6. After 2 failed retries: stop, report failure to user

### Plan Execution Flow

When executing a PLAN.md:

1. Read the plan, extract waves
2. For each wave:
   - Sequential tasks: spawn execute-plan agent one at a time
   - Parallel tasks: spawn up to 3 execute-plan agents simultaneously
3. After each task: spawn verify agent
4. After wave complete: spawn state-sync agent
5. Present wave summary, ask user before proceeding to next wave

### Parallel Verification

When multiple coding agents ran in parallel on independent files:
1. Spawn one verify agent per coding agent's changeset (max 3 parallel)
2. Each verify agent runs focused tests: `composer test -- --filter=RelevantTest1|RelevantTest2`
3. After all parallel verifies complete, spawn one final verify agent for full suite
4. This catches cross-cutting regressions the focused tests might miss

Fallback: If any parallel verify fails, stop and report — don't run the full suite.

## Agent Spawn Templates

### Coding Agent
```
Task tool with subagent_type: "general-purpose"
Prompt: Read .claude/agents/coding.md for your role definition. Then: <task description>
Files to read: <list>
Expected output: summary of changes made
```

### Verify Agent (full suite)
```
Task tool with subagent_type: "general-purpose"
Prompt: Read .claude/agents/verify.md for your role definition. Then verify the changes.
```

### Verify Agent (focused — for parallel verify)
```
Task tool with subagent_type: "general-purpose"
Prompt: Read .claude/agents/verify.md for your role definition. Run ONLY focused tests: composer test -- --filter=<TestA|TestB>. Compare to baseline.
```

### State-Sync Agent
```
Task tool with subagent_type: "general-purpose"
Prompt: Read .claude/agents/state-sync.md for your role definition. Then sync state for: <summary>
```

### Execute-Plan Agent
```
Task tool with subagent_type: "general-purpose"
Prompt: Read .claude/agents/execute-plan.md for your role definition. Then implement task <id> from <plan-path>.
```

### Web Agent
```
Task tool with subagent_type: "general-purpose"
Prompt: Read ~/.claude/agents/web.md for your role definition. [Optional: Read <overlay-path> first.] <query>
```

## Context Budget

- Keep orchestrator context under 20% — delegate, don't implement
- If context exceeds 50%, commit work, update state, stop with instructions for fresh session
- Never read large files directly — send an Explore agent instead
- Store summaries, not full outputs, from agent returns

## Agent Crash Recovery

After every agent spawn, before processing the result:

1. Check if the agent returned a valid structured response
2. Run `git diff` to check for uncommitted changes
3. If agent did not return cleanly:
   - `git stash` any uncommitted changes
   - Report to user: "Agent failed mid-task. Changes stashed. Review with `git stash show -p`."
4. If agent returned PARTIAL or BLOCKED status, present findings to user before continuing

## Rules

- Do NOT implement code changes directly (except quick fixes per the Quick Fix Path) — delegate to a coding agent
- Do NOT run tests directly — always delegate to verify agent
- DO read STATE.md, PLAN.md, and git status directly (small files, needed for routing)
- Always confirm with user before starting work or moving to next wave
- Orchestrator handles commits after verify passes (exception: execute-plan agent commits per task)
