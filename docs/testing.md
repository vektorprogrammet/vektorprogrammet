# Testing

## Quick Reference

```bash
# Via composer scripts (recommended)
composer test                # Full suite (496 tests, ~3 min)
composer test:unit           # 183 tests, <1s
composer test:controller     # 131 tests, ~110s
composer test:availability   # 182 tests, ~67s

# Direct phpunit (for filters/flags)
bin/phpunit --filter="SorterTest"
bin/phpunit --order-by=defects --stop-on-failure
```

For the full suite, `composer test` handles memory limits and config automatically. Use `bin/phpunit` directly when you need custom flags like `--filter` or `--stop-on-failure`.

## Workflow

```
While coding:   --filter="RelevantTest"              1-10s
After a fix:    --testsuite=unit                      <1s
Before commit:  --order-by=defects --stop-on-failure  fast fail
Pre-push:       full suite (no flags)                 ~3 min
```

## Environment

- **PHP 8.4**: `/usr/local/opt/php@8.4/bin/php`
- **SQLite**: test DB at `var/data/test.db`
- **Sandbox**: tests must run with sandbox disabled
- **Memory**: 256M limit (set by `composer test`; default 128M is insufficient)
- **Credentials**: `assistent`/`teammember`/`teamleader`/`admin` — all password `1234`

## Known Failures

None. All 496 tests pass (1150 assertions).

## Baseline Tracking

Test counts are tracked in [`.planning/test-baseline.md`](../.planning/test-baseline.md). Update after each commit. If failure count grows, investigate before committing.

## CI

Tests run automatically in GitHub Actions on push to `master` and on all PRs. The CI workflow also runs lint (`composer lint`) and static analysis (`composer analyse`) as separate parallel jobs. See [`.github/workflows/ci.yml`](../.github/workflows/ci.yml).

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
