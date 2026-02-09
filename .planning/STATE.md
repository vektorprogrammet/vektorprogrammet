# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-09
**Phase**: Sprint 7b (Fix Sf6 Test Regressions) — COMPLETE
**Branch**: `modernize/sprint-1-remove-dead-dependencies`
**Commits**: `8d2cb493` (Sf6 upgrade), `f6bbe7a1` (ContainerAwareCommand + Twig), `0a2918d1` (test fixes), pending (fix 15 regressions)

## Progress

- [x] Sprint 1: Remove dead dependencies
- [x] Sprint 2: Package upgrades
- [x] Sprint 3: AppBundle → App namespace
- [x] Sprint 4: Directory restructure
- [x] Sprint 5: Symfony 3.4 → 4.4
- [x] Sprint 6: Symfony 4.4 → 5.4 (committed: `5d4f2a15`)
- [x] Sprint 7: Symfony 5.4 → 6.4 — COMPLETE (3 commits)
- [x] Sprint 7b: Fix 15 Sf6 test regressions — COMPLETE
- [x] Agent tooling: composer scripts, php-cs-fixer, PHPStan, CI workflow
- [ ] Sprint 8-9: Frontend, cleanup

## Sprint 7b: Fix 15 Sf6 Test Regressions — COMPLETE

Fixed all 15 regressions. **496 tests, 2 failures** (CompanyEmailMaker only).

### Fix 1: ReversedRoleHierarchy Sf6 API (5 tests fixed)
- Removed `new Role()` (private ctor in Sf6) and `getReachableRoles()` (removed in Sf6)
- Replaced with `getReachableRoleNames()` which takes/returns string[]
- Fixed BaseController wrong import: `App\Utils\ReversedRoleHierarchy` → `App\Role\ReversedRoleHierarchy`

### Fix 2: Twig 3 Form Prototype Rendering (4 tests fixed)
- `repeatable_question.html.twig`: replaced `form_rest()` on prototypes with `setRendered()` calls
- Sf6 strict mode prevents rendering already-rendered fields; `form_rest()` on prototypes triggered this
- Added `{% do questions.setRendered() %}` at end to prevent parent `form_rest(form)` from re-rendering

### Fix 3: Receipt Templates (6 tests fixed)
- `receipt_viewer.html.twig`: null guards for `receipt.picturePath` — Sf6 `asset()` requires string, not null
- `edit_receipt.html.twig`: moved `{% block breadcrumb %}` outside `{% if %}` — Twig 3 prohibits blocks inside conditionals
- `edit_receipt.html.twig`: null guard on `asset(receipt.picturePath)` in JS section

### Fix 4: AccessControlService Bootstrap (all tests)
- Made `preloadCache()` lazy — moved from constructor to `ensureCacheLoaded()` called on first access
- Constructor `preloadCache()` opened SQLite connection before test DB existed, causing disk I/O errors on schema:create

## Test Results (496 tests)

**0 errors + 2 failures = 2 total** (both pre-existing CompanyEmailMaker locale)

## Key Decisions Made

- `getDoctrine()` and `get()` re-added as bridge methods to BaseController (deferred to Sprint 8 for proper DI)
- `session` service removed from subscribed services — use `request_stack->getSession()`
- `security.password_encoder` → `security.password_hasher` in subscribed services
- AccessControlService cache is now lazy (loaded on first access, not in constructor)

## CI Workflow

Replaced broken `tests.yml` (Sf 3.4/PHP 7.3) and `lintAndTest.yml` (missing runs-on) with unified `.github/workflows/ci.yml`:
- **lint**: `composer lint` (php-cs-fixer --dry-run)
- **analyse**: `composer analyse` (PHPStan level 1, needs `cache:clear --env=dev` first)
- **test**: `composer test` (full PHPUnit suite, PHP 8.1 matrix)
- `build.yml` (SonarCloud) unchanged

## Deferred to Sprint 8

- getDoctrine() → injected repos (257 calls)
- $this->get() → constructor DI (149 calls)
- annotations → PHP 8 attributes (663)
- Remove sensio/framework-extra-bundle + doctrine/annotations

## Reference

- Test commands & workflow: `docs/testing.md`
- Architecture details: `docs/architecture.md`
