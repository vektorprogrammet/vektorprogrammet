---
name: fresh-context-planner
description: Transform ideas into atomic task plans preventing context rot. Use when user says "plan project", "create roadmap", "break down", "new project", or describes feature.
allowed-tools: Read, Write, Grep, Glob
---

# Fresh Context Planner

Each task fits ~50% of fresh 200k context.

Context quality: 0-30% peak, 50%+ rushing, 70%+ hallucinations.

## Workflow

### Interview (One at a Time)

**What**: Goals, outcome, "done" definition
**How**: Stack, existing code, constraints
**Scope**: v1, v2, out-of-scope
**Edge**: Failures, weird inputs

### PROJECT.md

```markdown
# Project: <name>

## Problem

<1-2 sentences>

## Solution

<2-3 sentences>

## Success

- [ ] Observable criterion 1
- [ ] Observable criterion 2

## Constraints

Stack: <tech>

## Out of Scope

- <not doing>
```

### ROADMAP.md

```markdown
## Milestone 1: Foundation

Phase 1: Setup, models (2-3 tasks each)
Phase 2: API endpoints
Phase 3: Auth
```

### Per Phase: DISCUSS.md

```markdown
# Phase 1: Setup

## Intent

<what this accomplishes>

## Deliverables

- [ ] Thing 1

## Questions

- [ ] Decision point?
```

### Execution Plan: PLAN.md

```xml
<plan id="01-01">
  <task id="1">
    <description>Initialize project</description>
    <files>composer.json, config/</files>
    <action>
      - Install dependencies
      - Configure settings
    </action>
    <verification>
      - composer test passes
    </verification>
  </task>
</plan>

<execution-strategy>
  <wave id="1" mode="sequential">
    <tasks>1, 2</tasks>
  </wave>
</execution-strategy>
```

## Task Sizing

**Too Big**: "Build auth", "All CRUD"
**Right**: "Create User model", "Add POST /users"

Rules: 1-3 files, 50-150 lines, 15-30 min, clear verification

## Waves

Sequential: dependent tasks
Parallel: independent tasks

## STATE.md

```markdown
# State: <name>

**Updated**: <date>
**Phase**: 01-setup
**Next**: Phase 02

## Decisions

- <key decision + why>

## Deferred

- <thing> → v2

## Next Session

Setup done. Next: <specific next step>.
```

Structure:

```
.planning/
├── PROJECT.md
├── ROADMAP.md
├── STATE.md
└── phases/
    └── 01-setup/
        └── 01-01-PLAN.md
```
