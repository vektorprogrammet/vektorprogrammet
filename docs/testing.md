# Testing

## Quick Reference

```bash
# Full suite (496 tests, ~3 min)
/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist

# By suite
bin/phpunit --testsuite=unit          # 183 tests, <1s   (entities, services, utils)
bin/phpunit --testsuite=controller    # 131 tests, ~110s (functional controller tests)
bin/phpunit --testsuite=availability  # 182 tests, ~67s  (smoke test: all URLs x auth levels)

# Single test class
bin/phpunit --filter="SorterTest"

# Failed tests first, stop on first failure
bin/phpunit --order-by=defects --stop-on-failure
```

Replace `bin/phpunit` with `/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist` if running outside the project root or if the shorthand doesn't work.

## Test Suites

| Suite | Tests | Time | What it covers |
|-------|-------|------|----------------|
| `unit` | 183 | <1s | Entity unit tests, services, SMS, form types, utils |
| `controller` | 131 | ~110s | Functional tests per controller (login, submit forms, check responses) |
| `availability` | 182 | ~67s | Smoke test hitting every URL with different auth levels |

## Recommended Workflow

```
While coding:   --filter="RelevantTest"              1-10s
After a fix:    --testsuite=unit                      <1s
Before commit:  --order-by=defects --stop-on-failure  fast fail
Pre-push:       full suite (no flags)                 ~3 min
```

## Known Failures (2)

`CompanyEmailMakerTest::testNorwegianCharacters` and `testAccentCharacters` fail on macOS due to missing `nb_NO` locale. Pre-existing since Sprint 1.

## Environment Requirements

- **PHP 8.4**: `/usr/local/opt/php@8.4/bin/php`
- **SQLite**: test DB at `var/data/test.db`, backup at `var/data/test.db.bk`
- **Sandbox**: tests must run with sandbox disabled (SQLite writes + vendor reads)
- **Memory**: 512M limit required

## Test Database

The bootstrap (`tests/bootstrap.php`) handles DB setup automatically:
1. Deletes `test.db` and `test.db.bk`
2. Creates schema via `doctrine:schema:create`
3. Loads fixtures via `doctrine:fixtures:load`
4. Backs up to `test.db.bk`

Each test's `tearDown()` restores from `test.db.bk`, so tests are isolated.

If you get stale DB errors, clear everything:
```bash
rm -f var/data/test.db var/data/test.db.bk && rm -rf var/cache/test/
```

## Test Credentials

| Username | Password | Role |
|----------|----------|------|
| `assistent` | `1234` | ROLE_USER (assistant) |
| `teammember` | `1234` | ROLE_TEAM_MEMBER |
| `teamleader` | `1234` | ROLE_TEAM_LEADER |
| `admin` | `1234` | ROLE_ADMIN |

## Test Architecture

- `BaseWebTestCase` provides `createAssistantClient()`, `createTeamLeaderClient()`, etc.
- Clients are cached as static properties per class (Sf6 singleton kernel)
- `BaseKernelTestCase` for service-level tests without HTTP
- `AvailabilityFunctionalTest` uses data providers to test URL lists per auth level

## Timing Profile (top 10 slowest)

| Test Class | Tests | Time | % |
|-----------|-------|------|---|
| AvailabilityFunctionalTest | 182 | 66.5s | 37% |
| AdmissionAdminControllerTest | 13 | 12.1s | 7% |
| InterviewControllerTest | 9 | 11.8s | 7% |
| ReceiptControllerTest | 10 | 10.2s | 6% |
| SchoolAdminControllerTest | 8 | 8.2s | 5% |
| TeamAdminControllerTest | 10 | 7.0s | 4% |
| AccessRuleControllerTest | 4 | 6.6s | 4% |
| SurveyPopUpControllerTest | 4 | 5.6s | 3% |
| MailingListControllerTest | 2 | 5.1s | 3% |
| ExecutiveBoardControllerTest | 6 | 4.8s | 3% |
| *Unit tests (all 171)* | 171 | <1s | <1% |
