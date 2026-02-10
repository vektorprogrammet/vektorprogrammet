---
name: plan
description: Interactive planner — interviews user, explores codebase, produces PLAN.md with agent-ready tasks. Use when "plan", "break down", "new feature", or scope is vague/big.
allowed-tools: Read, Write, Edit, Glob, Grep, Bash(*), Task(Explore)
---

# Plan

Interactive planner that produces agent-executable PLAN.md files. Works in the main context (needs user interaction) but delegates codebase exploration to agents.

## Workflow

### 1. Interview (one question at a time)

Ask these in order, one at a time. Skip if already answered:

- **What**: What's the goal? What does "done" look like?
- **How**: Any constraints? Existing patterns to follow? Tech decisions already made?
- **Scope**: What's v1 vs deferred? What's explicitly out of scope?
- **Edge cases**: What could go wrong? What inputs are unexpected?

Keep questions concise. Don't over-interview — 2-4 questions usually suffice.

### 2. Explore Codebase

Delegate to Explore agent(s) to understand the relevant code:

```
Task tool with subagent_type: "Explore"
Prompt: <specific question about the codebase relevant to this plan>
```

Run multiple Explore agents in parallel if investigating independent areas.

Synthesize findings into a brief summary — don't dump raw output.

### 3. Produce PLAN.md

Write to `.planning/phases/<phase-name>/PLAN.md` using the template from `.claude/skills/plan/templates.md`.

Each task must specify:
- **Agent type**: coding, execute-plan, verify
- **Context spec**: files to read, information to pass
- **Verification criteria**: what must be true after the task
- **Dependencies**: which tasks must complete first

Organize tasks into waves:
- **Sequential**: tasks that depend on each other
- **Parallel**: independent tasks (max 3 per wave)

### 4. Review with User

Present the plan summary to the user before writing to disk:
- List each wave with task count and mode (sequential/parallel)
- Highlight any tasks that touch shared files or have complex dependencies
- Ask: "Does this plan look right? Any tasks to add, remove, or reorder?"
- Only write PLAN.md after user confirms

### 5. Produce/Update STATE.md

Update `.planning/STATE.md` with:
- New phase added
- Link to the new PLAN.md
- Next steps

### 6. Handoff

End with: **"Run `/orchestrate` to execute this plan."**

## Task Sizing Guide

**Too big** (split further):
- "Build authentication system"
- "All CRUD operations for User"
- Touches more than 5 files
- Would take >30 min in a fresh context

**Right size**:
- "Create User entity with fields X, Y, Z"
- "Add POST /users endpoint with validation"
- 1-5 files, 50-200 lines changed
- Clear single verification (one test command or check)

**Too small** (merge with neighbor):
- "Add one import statement"
- "Rename a single variable"

## Rules

- Never produce a plan without interviewing first
- Always explore the codebase before planning (don't guess at file locations or patterns)
- Always get user confirmation on the plan before writing to disk
- Each task must be completable by an agent with NO prior context
- Include enough file paths and context in each task that an agent can start cold
- Keep the plan file itself under 200 lines — if longer, split into sub-plans
