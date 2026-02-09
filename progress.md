# Modernization Progress

## Sprints 1-4 — COMPLETE (Summary)

**Sprint 1** (branch `modernize/sprint-1-remove-dead-dependencies`, PR #1592): Removed 7 dead packages (sensio/distribution-bundle, sensio/generator-bundle, bcc/auto-mapper-bundle, twig/extensions, incenteev/composer-parameter-handler, nexylan/slack-bundle, php-http/guzzle6-adapter). Rewrote Slack integration to direct GuzzleHttp webhooks. Kept laminas bridge (needed until Sf4.4) and elfinder 9.x (needs Sf4.4 for v10).

**Sprint 2**: Replaced `egeloen/ckeditor-bundle` → `friendsofsymfony/ckeditor-bundle` (config key `ivory_ck_editor` → `fos_ck_editor`). Upgraded doctrine-migrations-bundle ^2.2, sentry-symfony ^3.0, knp-paginator-bundle ~2.8. Deferred doctrine-bundle, elfinder, knp-paginator major upgrades to Sprint 5.

**Sprint 3**: Renamed `AppBundle\` → `App\` namespace across 431 PHP files. Bundle shorthand (`AppBundle:Controller:action`, `AppBundle:Entity`) kept unchanged — Sf3.4 derives bundle name from class name, not namespace.

**Sprint 4**: Restructured directories: `web/`→`public/`, `app/config/`→`config/`, `app/Resources/views/`→`templates/`, `app/DoctrineMigrations/`→`migrations/`, `app/Resources/assets/`→`assets/`. Created symlink `app/Resources/views`→`templates/` for legacy template resolution. Updated all config paths from `kernel.root_dir` to `kernel.project_dir`.

All sprints: 496 tests, 1152 assertions, 2 pre-existing failures (CompanyEmailMakerTest).

---

## Sprint 5: Symfony 3.4 → 4.4 — COMPLETE

### What Was Done
1. **composer.json** — Split `symfony/symfony` into 30+ individual 4.4.* packages. Upgraded: doctrine-bundle ^2.0, fm-elfinder-bundle ^10.0, knp-paginator-bundle ^5.0, sensio/framework-extra-bundle ^5.6, fos/rest-bundle ^2.8, twig ^2.14. Removed laminas bridge. Added dotenv, error-handler.
2. **New kernel** — `src/Kernel.php` with `MicroKernelTrait` (no namespace, classmap autoloaded). Created `config/bundles.php`.
3. **New front controller** — `public/index.php` replaces app.php/app_dev.php/app_staging.php. `.env`/`.env.test` for env vars. `bin/console` rewritten.
4. **Removed AppBundle** — Converted 190 routes (`AppBundle:Controller:action` → FQCN), 47 DQL/Form refs (`AppBundle:Entity` → FQCN), 12 Twig `controller()` calls, 9 colon-syntax template includes (`:dir:file.twig` → `dir/file.twig`).
5. **Config** — Removed `templating:` section, `framework.router.resource`, `logout_on_user_change`. Added explicit Doctrine ORM mapping for `App\Entity`. Removed fm_elfinder `include_assets`.
6. **Namespace** — `Doctrine\Common\Persistence` → `Doctrine\Persistence` in 33 files.
7. **Role entity** — `__toString()` returns `getRole()` not `getName()`. Added `parent::__construct($role)`. Added `: string` return type.
8. **Form fixes** — `isSubmitted()` must precede `isValid()` in Sf4.4 (fixed in ReceiptController, ChangeLogController, TeamApplicationController).
9. **Deleted** — app/AppKernel.php, app/AppCache.php, app/autoload.php, src/App/AppBundle.php, old front controllers, app/Resources/views symlink, entire app/ dir.
10. **Moved** — translations/, templates/bundles/TwigBundle/, templates/bundles/FMElfinderBundle/.
11. **Tests** — Updated phpunit.xml.dist (KERNEL_CLASS env var) and tests/bootstrap.php for new kernel.

### What Was Deferred
- `Controller` → `AbstractController` (~100 `$this->get()` calls need refactoring)
- `symfony/flex` (not strictly needed)
- `AdvancedUserInterface` removal (works in 4.4, removed in 5.0)
- `Role` should stop extending `Symfony\Component\Security\Core\Role\Role` (removed in 5.0)

### Test Results
496 tests, 1150 assertions, 3 failures (all pre-existing), 2 warnings (pre-existing)

### Gotchas for Sprint 6
- `Controller` removed in Sf5.0 — migrate to `AbstractController`
- `AdvancedUserInterface` removed in 5.0 — implement `UserCheckerInterface`
- `Role` extends `Symfony\Component\Security\Core\Role\Role` — removed in 5.0
- `encoders:` → `password_hashers:` in 5.3
- `anonymous: ~` in firewalls removed in 5.0
- SwiftMailer → Symfony Mailer
- Event dispatch: controllers use old `dispatch($name, $event)` — Sf5.0 requires `dispatch($event, $name)`
- `preg_match()` warnings in StaticPrefixCollection: PHP 7.4 PCRE2 issue, goes away with PHP 8
- Test fixture credentials: `assistent/1234`, `teammember/1234`, `teamleader/1234`, `admin/1234` (`tests/BaseWebTestCase.php`)

---

## Reference: vektor-backend

Existing modernization at `github.com/vektorprogrammet/vektor-backend` reached Sf5.4/PHP 8.2. Key takeaways:
- `Controller` → `AbstractController` with constructor-injected `ManagerRegistry`
- `AdvancedUserInterface` → `UserInterface` + `PasswordAuthenticatedUserInterface`
- SwiftMailer → `symfony/mailer` with `MAILER_DSN`
- `encoders` → `password_hashers`, `enable_authenticator_manager: true`
- Used `rector/rector` for automated refactoring
- Reference files: `composer.json`, `config/packages/security.yaml`, `src/Entity/User.php`
- Consider `dama/doctrine-test-bundle` for faster DB test transactions
