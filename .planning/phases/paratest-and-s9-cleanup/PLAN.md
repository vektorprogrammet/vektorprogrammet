# Plan: ParaTest + S9-4 Cleanup

**Goal**: Speed up test suite with parallel execution, then clean up deprecated packages.
**Branch**: `modernize/sprint-1-remove-dead-dependencies`
**Baseline**: 496 tests, 1150 assertions, 0 failures (~3 min sequential)

---

## Wave 1: ParaTest (sequential — each task depends on previous)

### Task 1.1: Install ParaTest and add composer scripts
**Agent**: coding
**Files to modify**: `composer.json`
**Context**: Install `brianium/paratest:^6.0` as dev dependency. Add composer scripts:
- `"test:parallel": "@php -d memory_limit=256M vendor/bin/paratest -p4 --runner=WrapperRunner -c phpunit.xml.dist"`
- Keep existing `test` script unchanged (sequential fallback)
**Verify**: `composer install` succeeds, `vendor/bin/paratest --version` prints version
**Commit**: "Add ParaTest for parallel test execution"

### Task 1.2: Make test DB path dynamic with TEST_TOKEN
**Agent**: coding
**Files to modify**:
- `config/config_test.yml` — change SQLite path to include token suffix
- `tests/bootstrap.php` — make TestDataManager use token-aware paths
- `tests/BaseWebTestCase.php` — no changes needed (calls TestDataManager)
- `tests/BaseKernelTestCase.php` — no changes needed

**Context**: ParaTest sets `TEST_TOKEN` env var (unique int per worker: 1, 2, 3...).

**config/config_test.yml** — change the doctrine dbal path:
```yaml
# Before:
path: "%kernel.project_dir%/var/data/test.db"
dbname: "%kernel.project_dir%/var/data/test.db"
# After:
path: "%kernel.project_dir%/var/data/test%env(default::TEST_TOKEN)%.db"
dbname: "%kernel.project_dir%/var/data/test%env(default::TEST_TOKEN)%.db"
```
When TEST_TOKEN is unset (sequential mode), resolves to `test.db` (unchanged behavior).
When TEST_TOKEN=1, resolves to `test1.db`.

**tests/bootstrap.php** — make TestDataManager token-aware:
```php
class TestDataManager {
    static function getToken(): string {
        return getenv('TEST_TOKEN') ?: '';
    }
    static function getDbFile(): string {
        return 'test' . self::getToken() . '.db';
    }
    static function getBackupFile(): string {
        return 'test' . self::getToken() . '.db.bk';
    }
    // Update deleteDatabase(), backupDatabase(), restoreDatabase() to use getDbFile()/getBackupFile()
}
```

**Verify**: `composer test` still passes (sequential, no TEST_TOKEN). Then `composer test:parallel` runs without DB locking errors.
**Commit**: "Support per-worker SQLite databases for parallel testing"

### Task 1.3: Verify and tune parallel execution
**Agent**: verify
**Context**: Run `composer test:parallel` and confirm:
1. All 496 tests pass
2. No "database is locked" errors
3. Wall time is significantly less than sequential (~1 min target with -p4)
4. Run sequential `composer test` to confirm no regression
If tests fail with -p4, try -p2. Report results.
**No commit** — verification only.

---

## Wave 2: S9-4 Package Cleanup (sequential)

### Task 2.1: Remove sentry-symfony
**Agent**: coding
**Files to modify**:
- `composer.json` — remove `"sentry/sentry-symfony": "^5.0"` from require (note: was already bumped to ^5.0 in a reverted attempt, check actual current value which should be `"^4.0"`)
- `config/bundles.php` — remove line 17: `Sentry\SentryBundle\SentryBundle::class => ['prod' => true, 'staging' => true],`
- `config/config_prod.yml` — remove the `sentry:` section (lines 37-38: `sentry:\n    dsn: '%sentry_address%'`)
- `config/parameters.yml.dist` — remove `sentry_address: xxxx` (line 64)

**Context**: Sentry has zero code usage in src/. It's config-only, registered for prod/staging only. The v4→v5 upgrade pulls Symfony v7 transitive deps that break the app. Removing it entirely is simpler.

After removing from composer.json, run `composer update --lock` to update the lock file.

**Verify**: `composer install` succeeds, `composer test` passes (496 tests, 0 failures).
**Commit**: "Remove sentry-symfony (unused, v5 incompatible with Sf6.4 deps)"

### Task 2.2: Upgrade knp-paginator-bundle v5→v6
**Agent**: coding
**Files to modify**:
- `composer.json` — change `"knplabs/knp-paginator-bundle": "^5.0"` to `"^6.0"`

**Context**: KNP Paginator is used in 3 controllers and 3 templates:
- Controllers: `ArticleController.php`, `FeedbackController.php`, `ArticleAdminController.php`
- All use `PaginatorInterface::paginate($query, $page, $limit)` — standard API
- Templates use `knp_pagination_render(pagination)` and iterate `{% for item in pagination %}`
- Config in `config/config.yml` sets Bootstrap v4 template

The v5→v6 upgrade should be API-compatible for this usage. Key changes in v6:
- Requires PHP 8.1+ (we have 8.4 ✓)
- Requires Symfony 5.4+ (we have 6.4 ✓)
- PaginatorInterface::paginate() signature unchanged

Run `composer require "knplabs/knp-paginator-bundle:^6.0" --update-with-dependencies`.
If Symfony components float to v7, add explicit pins for the offending transitive deps
(e.g., `symfony/string`, `symfony/property-access`, etc.) at `6.4.*` in composer.json require section.

**Verify**: `composer install` succeeds. Check `composer show knplabs/knp-paginator-bundle` shows v6.x. `composer test` passes (496 tests, 0 failures).
**Commit**: "Upgrade knp-paginator-bundle to v6"

---

## Done Criteria
- `composer test` (sequential): 496 tests, 0 failures
- `composer test:parallel` (parallel): 496 tests, 0 failures, ~1 min wall time
- sentry-symfony removed from project
- knp-paginator-bundle at v6.x
- All changes committed, branch pushed
