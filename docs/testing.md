# Testing

## Quick Reference

```bash
# Full suite (496 tests, ~3 min)
/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist

# By suite
bin/phpunit --testsuite=unit          # 183 tests, <1s
bin/phpunit --testsuite=controller    # 131 tests, ~110s
bin/phpunit --testsuite=availability  # 182 tests, ~67s

# Single test class
bin/phpunit --filter="SorterTest"

# Failed tests first, stop on first failure
bin/phpunit --order-by=defects --stop-on-failure
```

Replace `bin/phpunit` with `/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist` if running outside the project root or if the shorthand doesn't work.

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
- **Memory**: 512M limit required
- **Credentials**: `assistent`/`teammember`/`teamleader`/`admin` — all password `1234`

## Known Failures

None. All 496 tests pass.

## More Details

File→test mapping, timing profiles, DB internals, test architecture: [`docs/testing-details.md`](testing-details.md)
