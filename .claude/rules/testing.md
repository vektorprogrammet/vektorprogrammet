---
paths:
  - "tests/**/*.php"
  - "phpunit.xml.dist"
---

# Testing

## Running Tests

`composer test` not `php bin/phpunit`. Without this: missing `-d memory_limit=256M` → OOM on large suites. Always `dangerouslyDisableSandbox: true` — SQLite writes + vendor reads require it.

Baseline: 536 tests, 1340 assertions, 0 failures. Never commit if new failures appear.

Suites: `composer test:unit` (fast, <1s), `composer test:controller` (~110s), `composer test:availability` (~67s), `composer test:parallel` (all, 4 workers, ~103s).

## Test Classes

Integration tests: extend `BaseWebTestCase`. Use role-specific clients:
- `createAnonymousClient()`, `createAssistantClient()`, `createTeamMemberClient()`, `createTeamLeaderClient()`, `createAdminClient()`
- Credentials: username = role name (e.g. `admin`), password = `1234`

Unit tests: extend `BaseKernelTestCase`. Calls `TestDataManager::restoreDatabase()` in tearDown.

API tests: extend `BaseWebTestCase`. Use `getJwtToken('admin')` helper for JWT auth. Test DTOs/processors via HTTP, not by instantiating processors directly.

## Fixtures

SQLite test DB. `TestDataManager` handles create/fixture/backup automatically. Pre-configured test users in fixtures (admin, teamleader, teammember, assistent). Per-worker DB isolation via `TEST_TOKEN` for parallel runs.

## Known Issues

`testShouldSendInfoMeetingNotification` flaky near midnight — time-dependent logic. Retry if fails at 23:5x/00:0x.
