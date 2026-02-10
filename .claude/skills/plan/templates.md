# Plan Templates

Loaded on demand by the `/plan` skill. These are reference templates — adapt to fit the project.

## PLAN.md Template

```xml
<plan phase="<phase-name>" created="<date>">

  <wave id="1" mode="sequential">
    <task id="1">
      <description>Short description of what to do</description>
      <agent>coding</agent>
      <context>
        <read>src/Entity/User.php</read>
        <read>src/Controller/UserController.php</read>
        <info>The User entity currently has fields: id, name, email</info>
      </context>
      <action>
        - Step 1: do X
        - Step 2: do Y
      </action>
      <verification>
        - composer test --filter=UserTest passes
        - New field exists in entity
      </verification>
    </task>
  </wave>

  <wave id="2" mode="parallel">
    <task id="2">
      <description>Independent task A</description>
      <agent>coding</agent>
      <context>
        <read>src/Service/FooService.php</read>
      </context>
      <action>
        - Implement feature A
      </action>
      <verification>
        - composer test --filter=FooTest passes
      </verification>
    </task>
    <task id="3">
      <description>Independent task B</description>
      <agent>coding</agent>
      <context>
        <read>src/Service/BarService.php</read>
      </context>
      <action>
        - Implement feature B
      </action>
      <verification>
        - composer test --filter=BarTest passes
      </verification>
    </task>
  </wave>

</plan>

<execution-strategy>
  Wave 1: sequential — task 1 must complete first (foundation)
  Wave 2: parallel — tasks 2 and 3 are independent
</execution-strategy>
```

## STATE.md Template

```markdown
# State: <Project Name>

**Updated**: <date>
**Phase**: <current phase>
**Branch**: `<branch-name>`

## Progress

- [x] Phase 1: <description> (`<commit-hash>`)
- [ ] Phase 2: <description>

## Current Session

<What happened this session — 2-3 bullets>

## Known Issues

- <issue description>

## Next Steps

<Specific next action — not vague>

## Reference

- Plan: `.planning/phases/<phase>/PLAN.md`
- Test baseline: `.planning/test-baseline.md`
```

## PROJECT.md Template

```markdown
# Project: <Name>

## Problem

<1-2 sentences describing what needs to change and why>

## Solution

<2-3 sentences describing the approach>

## Success Criteria

- [ ] Observable criterion 1
- [ ] Observable criterion 2

## Constraints

- Stack: <technology>
- <Other constraints>

## Out of Scope

- <Not doing this>
- <Deferred to later>
```

## Task Sizing Reference

| Metric | Too Small | Right Size | Too Big |
|--------|-----------|------------|---------|
| Files | 0-1 | 1-5 | 6+ |
| Lines | <20 | 50-200 | 300+ |
| Scope | Single rename | Feature slice | Whole feature |
| Verify | Trivial | 1 test command | Multiple suites |
