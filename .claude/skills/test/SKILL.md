---
name: test
description: Interactive targeted test runner. Suggests tests based on changed files, runs focused suites. Use when "test this", "run tests", "what tests cover this", or during active development.
allowed-tools: Read, Glob, Grep, Bash(*)
user-invocable: true
argument-hint: [file|suite|all]
---

# Test

Smart test runner that maps changed files to relevant tests. Complements the verify agent (full suite gate) with targeted testing during dev.

## Workflow

### No args: suggest tests for changed files

1. Run `git diff --name-only` (include staged: `git diff --name-only HEAD`)
2. Read `docs/testing-details.md` for the file-to-test mapping table
3. Match changed files to test classes using the mapping
4. Present suggested test commands:
   ```
   ## Changed Files → Tests

   | Changed File | Test | Suite | Est. Time |
   |---|---|---|---|
   | Controller/HomeController.php | HomeControllerTest | controller | ~1s |
   | Entity/User.php | UserEntityUnitTest | unit | <1s |

   Suggested command:
   composer test -- --filter='HomeControllerTest|UserEntityUnitTest'

   Run this? (yes / pick specific / all)
   ```
5. Wait for user confirmation, then run

### File arg (e.g., `/test HomeController`)

1. Read `docs/testing-details.md`
2. Find matching test by partial name match against the mapping table
3. If exactly one match: run it directly
4. If multiple matches: present choices and ask
5. If no match: suggest `AvailabilityFunctionalTest` as fallback

### Suite arg (e.g., `/test unit`)

Run the suite directly:
- `unit` → `composer test -- --testsuite=unit`
- `controller` → `composer test -- --testsuite=controller`
- `availability` → `composer test -- --testsuite=availability`

### `all` arg

1. Run `composer test` (full suite)
2. Read `.planning/test-baseline.md`
3. Compare results to baseline and report:
   ```
   ## Full Suite Result

   Tests: 496 (baseline: 496) ✓
   Assertions: 1150 (baseline: 1150) ✓
   Failures: 0
   Time: 180s

   Baseline: MATCH
   ```

## Constraints

- Always use `dangerouslyDisableSandbox: true` for all test commands
- Use `composer test` not raw `php bin/phpunit` (memory limit)
- Interactive: ask before running if multiple tests match
- Present results clearly with pass/fail and timing
