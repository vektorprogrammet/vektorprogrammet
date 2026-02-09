---
name: pr-prep
description: Prepare changes for PR. Lint, test, review diff, commit, push. Use when "ready for PR", "prep PR", "ship this", "open PR".
allowed-tools: Read, Grep, Glob, Bash(*)
---

# PR Prep

Changes done → PR ready. Last mile before merge.

## Workflow

### 1. Assess (parallel)

Run these in parallel:
- `git status` → what's changed
- `git diff` → review changes
- `git log main..HEAD` → commit history
- Project test command → tests pass?
- Project lint command → lint clean?

Auto-detect project commands:
- `composer.json` → `composer test`, `composer lint`
- `package.json` → npm/pnpm scripts
- `Makefile` → make targets
- `Cargo.toml` → cargo check/clippy/test
- `pyproject.toml` → ruff/pytest

### 2. Fix Issues

If lint/test failures:
- Auto-fix what's trivial (lint --fix, format)
- Report what needs human decision

### 3. Clean Commits

If messy history:
- Suggest squash/reword
- Draft commit messages
- User approves before rebase

If clean already: skip.

### 4. Diff Review

Review the full diff for:
- Debug code (console.log, var_dump, debugger, dd(), TODO)
- Secrets/credentials
- Unintended changes
- Summarize what PR does

### 5. Push + PR

```bash
git push -u origin <branch>
gh pr create --title "..." --body "..."
```

PR body format:

```markdown
## Summary

<1-3 bullets>

## Changes

- file1: what changed
- file2: what changed

## Testing

- [x] Unit tests pass
- [x] Lint clean
- [ ] Manual testing needed: <if applicable>
```

## Quick Mode

If everything is clean (tests pass, lint clean, good commits):
- Skip to push + PR
- No interactive review
