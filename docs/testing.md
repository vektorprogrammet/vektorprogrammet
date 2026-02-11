# Testing

## Quick Reference

```bash
# Via composer scripts (recommended)
composer test                # Full suite sequential (496 tests, ~193s)
composer test:parallel       # Full suite parallel -p4 (496 tests, ~103s)
composer test:coverage       # Full suite with HTML coverage report (output: var/coverage/)
composer test:unit           # 183 tests, <1s
composer test:controller     # 131 tests, ~110s
composer test:availability   # 182 tests, ~67s

# Direct phpunit (for filters/flags)
bin/phpunit --filter="SorterTest"
bin/phpunit --order-by=defects --stop-on-failure
```

For the full suite, `composer test` handles memory limits, JUnit logging, and config automatically. Use `bin/phpunit` directly when you need custom flags like `--filter` or `--stop-on-failure`.

Test results are saved to `var/test-results.xml` (sequential) and `var/test-results-parallel.xml` (parallel) via `--log-junit`.

## Code Coverage

⚠️ **Status: Fix in Progress** - Root cause identified, critical fixes being applied.

```bash
# Unit tests only (working)
phpdbg -qrr -d memory_limit=512M bin/phpunit --testsuite=unit --coverage-html var/coverage-unit

# Full suite (OOM at 1G - fixes in progress)
# composer test:coverage
```

**Current Baseline (unit tests only):**
- **Line Coverage**: 9.03% (1,130 / 12,511 lines)
- **Method Coverage**: 17.73% (385 / 2,172 methods)
- **Memory**: 148.5 MB (well under 512M limit)
- **Note**: Low coverage expected - unit tests only cover entities/services, not controllers

**Setup:**
- Driver: phpdbg (built into PHP, no extensions needed)
- Config: `phpunit.xml.dist` has `<coverage>` section with exclusions
- Output: `var/coverage/` (HTML report, gitignored)

**Root Cause (identified 2026-02-11):**
- Static client caching in `BaseWebTestCase` accumulates clients across test classes
- No EntityManager cleanup allows entity graphs to persist in UnitOfWork
- Coverage overhead amplifies memory usage → OOM at 1G during full suite

**Critical Fixes (in progress):**
1. Add `EntityManager::clear()` in `BaseWebTestCase::tearDown()` (50-200 MB savings)
2. Reset static clients in `tearDownAfterClass()` (500-1000 MB savings)

**Expected Result:** Full suite coverage should run without OOM after fixes applied.

## Workflow

```
While coding:   --filter="RelevantTest"              1-10s
After a fix:    --testsuite=unit                      <1s
Before commit:  --order-by=defects --stop-on-failure  fast fail
Pre-push:       full suite (no flags)                 ~3 min
```

## Parallel Testing & Environment

ParaTest runs with 4 workers, isolated SQLite DBs per worker. See [testing-details.md](testing-details.md) for ParaTest configuration, environment requirements, and test credentials.

## Known Failures

None.

## CI

See overview.md for CI job breakdown.

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
