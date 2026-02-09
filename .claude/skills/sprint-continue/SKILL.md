---
name: sprint-continue
description: Resume a multi-session sprint. Reads planning docs, verifies tests against baseline, summarizes state, and waits for confirmation. Use at session start, "where was I", "continue", "resume", or "pick up where I left off".
allowed-tools: Read, Grep, Glob, Bash(*)
---

# Sprint Continue

Resume work from where the last session left off. Restores context, verifies test health, and identifies next steps.

## Steps

1. **Restore context**: Read `.planning/STATE.md` and any phase files referenced in it.
2. **Check git state**: Run `git status`, `git log --oneline -5`, and `git branch` to understand current position.
3. **Verify test health**: Run the full test suite:
   ```bash
   rm -f var/data/test.db* && php -d memory_limit=512M bin/phpunit --no-coverage
   ```
4. **Compare to baseline**: Read `.planning/test-baseline.md` and compare current results. Flag any regressions.
5. **Summarize**: Present a concise summary:
   - What was completed last session
   - Current branch and recent commits
   - Test status vs baseline (green/regression)
   - What's next in the plan
6. **Ask**: Confirm with user before proceeding with any work.

## Notes
- Do NOT start implementing anything until the user confirms
- If tests regress from baseline, that becomes the top priority
- If `.planning/STATE.md` doesn't exist, say so and ask what the user wants to work on
