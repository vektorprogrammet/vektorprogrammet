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

None. All 496 tests pass (1150 assertions).

## CI

See overview.md for CI job breakdown.

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
