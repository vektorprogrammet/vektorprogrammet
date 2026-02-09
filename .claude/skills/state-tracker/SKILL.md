---
name: state-tracker
description: Conventions for persisting project state across sessions. How to write and update STATE.md.
user-invocable: false
---

# State Tracker

Each new session forgets. STATE.md = memory.

## STATE.md Format

```markdown
# State: <name>

**Updated**: <date>
**Phase**: <current phase>
**Next**: <next task>

## Progress

- [x] Phase 01: Setup (3/3)
- [ ] Phase 02: Models (0/4)

## Decisions

### <date>: <decision>

**Why**: <reasoning>
**Impact**: <what this affects>

## Deferred

- <thing> → v2
- <thing> → later

## Next Session

### Done
<what was completed>

### Current
<what's in progress>

### Need
<specific requirements for next step>

### Constraints
<anything to remember>
```

## When to Update

- **After task completion**: Append task status with commit hash
- **After decision**: Add to Decisions section with date and reasoning
- **At session end**: Update Next Session with specific next steps
- **On scope change**: Update Progress and Deferred

## Rules

- Keep concise: no code snippets, no full history
- Keep actionable: specific next steps, not vague descriptions
- Update the date on every change

Output: `.planning/STATE.md`
