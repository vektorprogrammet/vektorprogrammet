# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-09
**Phase**: Sprint 7 (Symfony 5.4 → 6.4) — Phase 1 COMPLETE, Phases 2-3 mostly done
**Branch**: `modernize/sprint-1-remove-dead-dependencies` (all uncommitted)

## Progress

- [x] Sprint 1: Remove dead dependencies
- [x] Sprint 2: Package upgrades
- [x] Sprint 3: AppBundle → App namespace
- [x] Sprint 4: Directory restructure
- [x] Sprint 5: Symfony 3.4 → 4.4
- [x] Sprint 6: Symfony 4.4 → 5.4 (committed: `5d4f2a15`)
- [ ] Sprint 7: Symfony 5.4 → 6.4 — IN PROGRESS
- [ ] Sprint 8-9: Frontend, cleanup

## Sprint 7 Current State

### Phase 1: Composer Upgrade + Boot-Blocking Config — COMPLETE

All composer deps upgraded, kernel boots, 496 tests run.

**Changes (all uncommitted):**

1. **composer.json**: All `symfony/*` → `6.4.*`, php `>=8.1`, removed `swiftmailer-bundle`, added `symfony/mailer: 6.4.*`, `doctrine/annotations: ^2.0`, `doctrine/dbal: ^3.0`, `doctrine/doctrine-migrations-bundle: ^3.0`, `twig/twig: ^3.0`, `twig/extra-bundle: ^3.0`, `helios-ag/fm-elfinder-bundle: ^12.0`, `php-cs-fixer: ^3.0`
2. **composer.lock**: Updated via `composer update --no-scripts`
3. **config/bundles.php**: Removed `SwiftmailerBundle` and `WebServerBundle`
4. **config/config.yml**: Removed `swiftmailer:` section, added `framework: mailer: dsn:`, updated `doctrine_migrations:`, removed `sensio_framework_extra: view: {annotations: true}`
5. **config/config_dev.yml**: Removed `swiftmailer: disable_delivery: true`
6. **config/config_test.yml**: Removed `swiftmailer:` section, `storage_id` → `storage_factory_id`
7. **config/config_staging.yml**: Removed `swiftmailer:` section
8. **config/security.yml**: Removed `enable_authenticator_manager: true`
9. **config/routing.yml**: ElFinder routes → FQCN
10. **src/Kernel.php**: `RouteCollectionBuilder` → `RoutingConfigurator`
11. **bin/console**: Fixed Dotenv constructor
12. **public/index.php**: Same Dotenv fix
13. **LogService.php**: PSR-3 v3 signatures
14. **User.php**: `eraseCredentials(): void`, `isEqualTo(): bool`
15. **31 DataFixture files**: `load(): void`, `getOrder(): int`
16. **9 SessionInterface → RequestStack files** (all subscribers + services)
17. **BaseController**: Added `getDoctrine()` + `get()` bridge methods, `doctrine` service, `password_hasher`, removed `session`
18. **3 controllers**: `$this->get('session')` → `$this->get('request_stack')->getSession()`
19. **2 controllers**: `UsernamePasswordToken` — removed credentials param
20. **1 controller**: `kernel.root_dir` → `kernel.project_dir`

### Phase 2: SwiftMailer → Symfony Mailer — COMPLETE

All source files migrated. `Swift_Message` → `Email`, `Swift_Mailer` → `Symfony\Component\Mailer\MailerInterface`.
Test files updated from `swiftmailer` profiler collector → `mailer` collector.

Files changed: Mailer.php, MailerInterface.php, EmailSender.php, Gmail.php, SlackMailer.php, InterviewManager.php, PasswordManager.php, UserRegistration.php, SurveyNotifier.php, all EventSubscribers.

### Phase 3: password_encoder → password_hasher — PARTIAL

- BaseController subscribed services updated
- Controller `$this->get('security.password_encoder')` → `security.password_hasher` (2 files)
- **REMAINING**: actual password hashing on user create/reset (PasswordReset test still fails)

### Phase 4-6: NOT STARTED

- ContainerAwareCommand → Command with DI (6 commands)
- Remaining Sf6 Breaking Changes (Twig 3 for...if, DBAL 3 lazy ghosts)
- Verification + Docs

## Test Results (496 tests)

**6 errors + 17 failures = 23 total**

Known pre-existing (15):
- AccessRule (5): upstream deleted feature
- Receipt (5): null asset/permissions + edit error
- Interview/Survey template (3): "already rendered"
- CompanyEmailMaker (2): macOS missing nb_NO locale

New from Sf6 migration (8):
- AdmissionAdmin (3): interview scheduling not redirecting (mailer integration)
- InterviewController (2): same scheduling issue
- PasswordReset (1): password not re-hashed on reset (needs password_hasher integration)
- /utlegg (1): receipt-related

## Key Decisions Made

- `getDoctrine()` and `get()` re-added as bridge methods to BaseController (deferred to Sprint 8 for proper DI)
- `session` service removed from subscribed services — use `request_stack->getSession()`
- `security.password_encoder` → `security.password_hasher` in subscribed services
- Schema creation: run `doctrine:schema:create --env=test` separately if test.db deleted (race condition in PHPUnit bootstrap)

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
