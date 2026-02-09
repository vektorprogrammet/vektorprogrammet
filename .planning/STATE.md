# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-09
**Phase**: Sprint 7 (Symfony 5.4 → 6.4) — ALL PHASES COMPLETE
**Branch**: `modernize/sprint-1-remove-dead-dependencies`
**Commits**: `8d2cb493` (Sf6 upgrade), `f6bbe7a1` (ContainerAwareCommand + Twig), `0a2918d1` (test fixes)

## Progress

- [x] Sprint 1: Remove dead dependencies
- [x] Sprint 2: Package upgrades
- [x] Sprint 3: AppBundle → App namespace
- [x] Sprint 4: Directory restructure
- [x] Sprint 5: Symfony 3.4 → 4.4
- [x] Sprint 6: Symfony 4.4 → 5.4 (committed: `5d4f2a15`)
- [x] Sprint 7: Symfony 5.4 → 6.4 — COMPLETE (3 commits)
- [ ] Sprint 8-9: Frontend, cleanup

## Sprint 7 Phases

### Phase 1: Composer Upgrade + Boot-Blocking Config — COMPLETE (commit `8d2cb493`)

All composer deps upgraded, kernel boots. Changes: composer.json/lock, config/*.yml, Kernel.php, bin/console, public/index.php, LogService PSR-3, User.php, 31 DataFixture files, 9 SessionInterface→RequestStack files, BaseController bridge methods, 3 controllers session fix, 2 controllers UsernamePasswordToken, 1 controller kernel.root_dir.

### Phase 2: SwiftMailer → Symfony Mailer — COMPLETE (commit `8d2cb493`)

All source + test files migrated. `Swift_Message` → `Email`, `Swift_Mailer` → `MailerInterface`.

### Phase 3: password_encoder → password_hasher — COMPLETE (commit `8d2cb493`, test fix in `0a2918d1`)

- BaseController subscribed services updated
- Controller `security.password_encoder` → `security.password_hasher` (2 files)
- PasswordReset test fixed (session carryover issue, not hashing)

### Phase 4: ContainerAwareCommand → Command with DI — COMPLETE (commit `f6bbe7a1`)

6 commands migrated. Command directory added to services.yml autowiring.

### Phase 5: Twig 3 Compatibility — COMPLETE (commit `f6bbe7a1`)

- `for...if` → `|filter()` in 4 templates
- `spaceless` filter: deprecation warnings only (Twig 3.12), not errors. Will break in Twig 4.

### Phase 6: Fix Sf6 Test Regressions — COMPLETE

Fixed all 6 genuine Sf6 regressions:
1. **Mailer default `from`**: `Mailer::send()` now adds default `from` when missing (Gmail did this in prod, but test/dev didn't)
2. **AdmissionAdminControllerTest line swap**: form fetched before setting values
3. **InterviewControllerTest debug code**: reverted
4. **PasswordResetControllerTest session**: fresh cookie jar per login attempt (Sf6 singleton client keeps session)

## Test Results (496 tests)

**1 error + 16 failures = 17 total (all pre-existing)**

- AccessRule (5): upstream deleted feature (3 controller + 2 availability)
- Receipt (6): `testCreate`, permissions x3, `testEdit` (error), `/utlegg`
- Interview schema (2): `testCreateSchema`, `testEditSchemas`
- Availability (2): `/kontrollpanel/intervju/skjema/1`, `/kontrollpanel/undersokelse/opprett`
- CompanyEmailMaker (2): macOS missing nb_NO locale

No Sf6 regressions remaining.

## Key Decisions Made

- `getDoctrine()` and `get()` re-added as bridge methods to BaseController (deferred to Sprint 8 for proper DI)
- `session` service removed from subscribed services — use `request_stack->getSession()`
- `security.password_encoder` → `security.password_hasher` in subscribed services
- Schema creation: run `doctrine:schema:create --env=test` separately if test.db deleted

## Deferred to Sprint 8

- getDoctrine() → injected repos (257 calls)
- $this->get() → constructor DI (149 calls)
- annotations → PHP 8 attributes (663)
- Remove sensio/framework-extra-bundle + doctrine/annotations

## Test Command

```bash
# Create schema first if test.db was deleted:
/usr/local/opt/php@8.4/bin/php bin/console doctrine:schema:create --env=test
# Then run tests:
/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist
```
