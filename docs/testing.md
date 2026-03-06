# Testing

## Quick Reference

```bash
# Via composer scripts (recommended)
composer test                # Full suite sequential (~175s)
composer test:parallel       # Full suite parallel -p4 (~67s)
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

⚠️ **Status: Deferred** - Coverage overhead on Doctrine/Symfony stack exceeds practical memory limits.

```bash
# Unit tests only (working)
phpdbg -qrr -d memory_limit=512M bin/phpunit --testsuite=unit --coverage-html var/coverage-unit

# Full suite (Deferred - requires >2GB per test, see troubleshooting.md)
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

**Memory Leak Fixes (applied):**
1. ✅ `EntityManager::clear()` in `BaseWebTestCase::tearDown()` - prevents entity accumulation
2. ✅ Static client reset in `tearDownAfterClass()` - prevents client accumulation

**Investigation Results (2026-02-11):**
- Memory leak fixes work for regular test runs (496 tests at 48.5 MB)
- Coverage overhead on Doctrine/Symfony operations is ~50x (single test: 5 MB → 512+ MB)
- Controller/availability test coverage requires 2GB+ memory per test
- **Decision**: Deferred - unit test coverage (9%) provides baseline until optimization feasible

**Per-suite coverage results**:
- ✅ Unit tests: 9.03% line coverage, 146 MB memory
- ❌ Controller tests: OOM at 1GB (Doctrine metadata + hydration under coverage)
- ❌ Availability tests: OOM at 1GB (full HTTP stack under coverage)

**Debugging approach**: When full suite coverage fails with OOM, run each suite separately with coverage to isolate which suite(s) cause the issue:
```bash
phpdbg -qrr -d memory_limit=1G bin/phpunit --testsuite=unit --coverage-html var/coverage-unit
phpdbg -qrr -d memory_limit=1G bin/phpunit --testsuite=controller --coverage-html var/coverage-controller
phpdbg -qrr -d memory_limit=1G bin/phpunit --testsuite=availability --coverage-html var/coverage-availability
```

## Workflow

```
While coding:   --filter="RelevantTest"              1-10s
After a fix:    --testsuite=unit                      <1s
Before commit:  --order-by=defects --stop-on-failure  fast fail
Pre-push:       full suite (no flags)                 ~175s
```

## Parallel Testing & Environment

ParaTest runs with 4 workers, isolated SQLite DBs per worker. See [testing-details.md](testing-details.md) for ParaTest configuration, environment requirements, and test credentials.

## Known Failures

**testShouldSendInfoMeetingNotification** (flaky near midnight) - Time-dependent test logic can fail during date boundary. Retry if fails at 23:5x or 00:0x.

## CI

See overview.md for CI job breakdown.

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
