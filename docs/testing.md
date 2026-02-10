# Testing

## Quick Reference

```bash
# Via composer scripts (recommended)
composer test                # Full suite sequential (496 tests, ~193s)
composer test:parallel       # Full suite parallel -p4 (496 tests, ~103s)
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

## Parallel Testing (ParaTest)

ParaTest runs tests across 4 workers using `WrapperRunner`. Each worker gets a unique `TEST_TOKEN` env var, which creates isolated SQLite DBs (`test1.db`, `test2.db`, etc.).

- Use `WrapperRunner` (not default runner) — it preserves static state within a worker, fewer double-boot errors
- `BaseWebTestCase::createClient()` has a catch-retry pattern for Sf6.4 double-boot (`LogicException`)
- Do NOT reset static clients in `tearDown` or call `ensureKernelShutdown()` preemptively — both cause 29+ failures

## Environment

- **PHP 8.5**: local dev environment
- **SQLite**: test DB at `var/data/test.db` (sequential) or `var/data/test{TOKEN}.db` (parallel)
- **Sandbox**: tests must run with sandbox disabled
- **Memory**: 256M limit (set by `composer test`; default 128M is insufficient)
- **Credentials**: `assistent`/`teammember`/`teamleader`/`admin` — all password `1234`

## Known Failures

None. All 496 tests pass (1150 assertions).

## CI

Tests run automatically in GitHub Actions on push to `master` and on all PRs. The CI workflow also runs lint (`composer lint`) and static analysis (`composer analyse`) as separate parallel jobs. See [`.github/workflows/ci.yml`](../.github/workflows/ci.yml).

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
