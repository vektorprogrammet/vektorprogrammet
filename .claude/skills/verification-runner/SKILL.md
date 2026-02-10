---
name: verification-runner
description: Goal-backward verification - what must be TRUE? Use when task completes, "verify", "run checks", or after changes.
user-invocable: false
allowed-tools: Read, Grep, Glob, Bash(*)
---

# Verification Runner

Ask: "what must be true?" not "what did we do?"

## Types of Checks

- **Build/compile**: Run build command, expect exit code 0
- **Lint**: Run linter, expect clean output
- **Tests**: Run test suite, compare to baseline
- **File existence**: Verify expected files exist
- **Content**: Read files, verify expected content is present

## Workflow

1. Load verification criteria from the task (or infer from what changed)
2. Auto-detect project commands:
   - `composer.json` → `composer test`, `composer lint`, `composer analyse`
   - `package.json` → npm/pnpm scripts
   - `Makefile` → make targets
   - `Cargo.toml` → cargo check/clippy/test
   - `pyproject.toml` → ruff/pytest
3. Run each check, capture exit code + output
4. If `.planning/test-baseline.md` exists, compare test counts to baseline
5. Write report

## Report Format

Write to `.verification/<task>.md`:

```markdown
# Verification: <task>

**Status**: PASSED | FAILED

## Checks

### <command> — PASSED
Exit: 0, Duration: 1.2s

### <command> — FAILED
Exit: 1
Error: <relevant output>
Suggested fix: <what to try>
```

## Modes

- **Quick**: build + lint (default)
- **Full**: build + lint + full test suite + baseline comparison

## On Failure

Report what failed, include the error output, suggest fix steps. Do NOT retry automatically — return control to the calling skill or user.
