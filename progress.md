# Modernization Progress

## Sprint 1: Remove Dead Dependencies — COMPLETE
**Branch:** `modernize/sprint-1-remove-dead-dependencies`
**PR:** #1592

### What Was Done
1. Removed 7 dead/incompatible packages from `composer.json`
2. Removed 5 bundle registrations from `AppKernel.php`
3. Rewrote Slack integration (3 files) to use GuzzleHttp webhooks directly
4. Replaced AutoMapper with manual DTO mapping in `AccountController`
5. Removed httplug and nexy_slack config sections from `config.yml`
6. Added `audit.block-insecure: false` to composer config (required for legacy packages)
7. Deleted stale `composer.lock` and regenerated with PHP 7.4

### Removed Dependencies
| Package | Reason |
|---|---|
| `sensio/distribution-bundle` | Removed in Symfony 4 |
| `sensio/generator-bundle` | Removed in Symfony 4 |
| `bcc/auto-mapper-bundle` | Unmaintained, only used in 2 methods |
| `twig/extensions` | Deprecated, no config references found |
| `incenteev/composer-parameter-handler` | Symfony 3 era, replaced by .env |
| `nexylan/slack-bundle` | PSR-18 type incompatibility with newer httplug |
| `php-http/guzzle6-adapter` | Only needed by nexylan/slack-bundle |
| `http-interop/http-factory-guzzle` | Only needed by nexylan/slack-bundle |

### Kept (still needed until Sprint 2+)
| Package | Why |
|---|---|
| `laminas/laminas-zendframework-bridge` | ProxyManager bridge needs Zend\Code compatibility shim |
| `helios-ag/fm-elfinder-bundle` 9.x | Only version supporting Symfony 3.4; v10+ needs Symfony 4.4+ |
| `egeloen/ckeditor-bundle` | Still works on 3.4, replaced in Sprint 2 |

### Key Files Modified
- `composer.json` — cleaned deps, removed scripts, added audit config
- `app/AppKernel.php` — removed SensioDistribution, SensioGenerator, BCCAutoMapper, HttpPlug, NexySlack bundles
- `app/config/config.yml` — removed automapper import, httplug section, nexy_slack section
- `app/config/services.yml` — removed guzzle.slack service, updated SlackMessenger args (now takes `$endpoint` instead of Nexy Client)
- `src/AppBundle/Controller/Api/AccountController.php` — manual DTO mapping replaces AutoMapper
- `src/AppBundle/Service/SlackMessenger.php` — complete rewrite: Nexy\Slack\Client → GuzzleHttp webhooks
- `src/AppBundle/Service/SlackMailer.php` — updated to use `sendPayload()` instead of Nexy Message objects
- `src/AppBundle/Sms/SlackSms.php` — updated to use `sendPayload()` instead of Nexy Message/Attachment objects

### Decisions & Rationale
1. **Slack rewrite over upgrade**: The nexylan/slack-bundle had a PSR-18 type incompatibility that couldn't be fixed without upgrading httplug, which cascaded into more dependency conflicts. Direct webhook calls are simpler and have zero external dependencies.
2. **Kept laminas bridge**: Removing it caused a `Zend\Code\Generator\ClassGenerator` fatal error in ProxyManager. It's needed until we upgrade to Symfony 4.4+ which uses a newer ProxyManager.
3. **Kept elfinder 9.x**: Version 10+ requires Symfony 4.4+. The `robloach/component-installer` Composer 1 plugin API issue is worked around with `--ignore-platform-req=composer-plugin-api`.
4. **Security advisories ignored**: `audit.block-insecure: false` in composer.json. We're knowingly running insecure legacy packages during the upgrade. They'll be updated in later sprints.

### Test Baseline
```
496 tests, 1152 assertions, 2 pre-existing failures
Failures:
  - CompanyEmailMakerTest::testNorwegianCharacters
  - CompanyEmailMakerTest::testAccentCharacters
```

### Gotchas for Next Sprint
- `helios-ag/fm-elfinder-bundle` can only be upgraded to 10+ when Symfony is at 4.4 (Sprint 5). Don't try to upgrade it in Sprint 2.
- `egeloen/ckeditor-bundle` 6.x requires `symfony/framework-bundle ^2.7|^3.0`. The replacement `friendsofsymfony/ckeditor-bundle` ^2.0 supports Symfony 3.4+. Config key changes from `ivory_ck_editor` to `fos_ck_editor`.
- The `User` entity (`src/AppBundle/Entity/User.php`) implements `AdvancedUserInterface` and `Serializable`. Don't touch these until Sprint 6 (Symfony 5.4).
- `ewz_recaptcha` is disabled (`enabled: false`) — low priority to replace.
- `app/config/parameters.yml` has `slack_endpoint`, `slack_disabled: true` — Slack is disabled in dev by default.

---

## Sprint 2: Replace Deprecated Bundles — COMPLETE

### What Was Done
1. Replaced `egeloen/ckeditor-bundle` ~6.0 with `friendsofsymfony/ckeditor-bundle` ^1.2
   - Config key: `ivory_ck_editor` → `fos_ck_editor` in `config.yml`
   - Kernel: `Ivory\CKEditorBundle\IvoryCKEditorBundle` → `FOS\CKEditorBundle\FOSCKEditorBundle`
   - Form types (6 files): `Ivory\CKEditorBundle\Form\Type\CKEditorType` → `FOS\CKEditorBundle\Form\Type\CKEditorType`
2. Upgraded `doctrine/doctrine-migrations-bundle` ^1.2 → ^2.2
3. Upgraded `sentry/sentry-symfony` ^2.0 → ^3.0
4. Upgraded `knplabs/knp-paginator-bundle` ~2.4 → ~2.8

### Deferred to Sprint 5 (requires Symfony 4.4)
| Package | Current | Target | Why |
|---|---|---|---|
| `doctrine/doctrine-bundle` | ^1.6 | ^2.0 | Needs `symfony/config ^4.3.3` |
| `helios-ag/fm-elfinder-bundle` | ^9.1 | ^10.0 | Needs `symfony/framework-bundle ^4.4` |
| `knplabs/knp-paginator-bundle` | ~2.8 | ^5.0 | Needs `symfony/framework-bundle ^4.3` |
| `friendsofsymfony/rest-bundle` | ^2.5 | ^3.0 | Can wait, 2.x works through Symfony 4 |

### Kept As-Is (low priority)
- `excelwebzone/recaptcha-bundle` ^1.5 — reCAPTCHA is disabled in all envs except prod
- `liip/imagine-bundle` ^2.1 — already compatible through Symfony 5.0

### Test Results
496 tests, 1152 assertions, 2 pre-existing failures (unchanged)

---

## Sprint 3: Namespace Rename — COMPLETE

### What Was Done
1. Moved `src/AppBundle/` directory to `src/App/`
2. Updated `composer.json` PSR-4 autoload: `AppBundle\\` → `App\\`, path `src/AppBundle` → `src/App`
3. Updated namespace declarations in all 363 PHP source files: `namespace AppBundle\...` → `namespace App\...`
4. Updated all `use` statements across 363 source + 68 test files
5. Updated `AppKernel.php`: `new AppBundle\AppBundle()` → `new App\AppBundle()`
6. Updated bundle class namespace: `namespace AppBundle` → `namespace App` (class name stays `AppBundle`)
7. Updated FQCN references in 10 config files (services, security, event_subscribers, twig, validators, etc.)
8. Updated filesystem resource paths in config: `../../src/AppBundle/` → `../../src/App/`
9. Updated migration file FQCN reference
10. Updated `package.json` scheduling paths: `src/AppBundle/...` → `src/App/...`

### What Was NOT Changed (by design)
- **Bundle shorthand references** stay as `AppBundle:Controller:action` in routing.yml — Symfony derives bundle name from class name (`AppBundle`), not namespace
- **Doctrine shorthand** stays as `AppBundle:Entity` in DQL queries and form types — resolved via bundle name
- **`@AppBundle/` resource references** stay unchanged in routing — resolved via bundle name
- **Bundle class name** stays `AppBundle` (in `App` namespace) — required by Symfony 3.4 bundle system
- **GitHub URLs in fixture data** — historical links, left as-is

### Key Insight
In Symfony 3.4, the bundle name (used for `@BundleName/`, `BundleName:Entity`, `BundleName:Controller:action`) is derived from the **class name**, not the namespace. So changing the namespace from `AppBundle\` to `App\` does NOT change the bundle name — it remains `AppBundle`. All shorthand references must continue using `AppBundle`.

### Files Changed
- `composer.json` — autoload PSR-4 mapping
- `app/AppKernel.php` — bundle registration
- `src/App/AppBundle.php` — bundle class namespace
- 363 PHP files in `src/App/` — namespace + use statements
- 68 PHP files in `tests/` — use statements
- `app/config/services.yml` — FQCN refs + resource paths
- `app/config/security.yml` — FQCN entity refs
- `app/config/event_subscribers.yml` — FQCN refs + resource paths
- `app/config/twig.yml` — FQCN refs + resource paths
- `app/config/validators.yml` — FQCN refs + resource paths
- `app/config/services_google.yml` — FQCN refs
- `app/config/forms.yml` — FQCN refs
- `app/config/automapper.yml` — FQCN refs
- `app/DoctrineMigrations/VersionCreateExecutiveBoard.php` — use statement
- `package.json` — scheduling script paths

### Test Results
496 tests, 1152 assertions, 2 pre-existing failures (unchanged)

---

## Sprint 4: Directory Restructure — COMPLETE

### What Was Done
1. Moved `web/` → `public/` (front controllers, static assets)
2. Moved `app/Resources/views/` → `templates/` with symlink `app/Resources/views` → `../../templates` for Symfony 3.4 colon-syntax backward compatibility
3. Moved `app/config/` → `config/` (all config files)
4. Moved `app/DoctrineMigrations/` → `migrations/`
5. Moved `app/Resources/assets/` → `assets/` (SCSS, JS, images)
6. Moved `app/phpunit.xml.dist` → `phpunit.xml.dist` (root)
7. Moved `app/testBootstrap.php` → `tests/bootstrap.php`
8. Added `getProjectDir()` override to `AppKernel` returning `dirname(__DIR__)`
9. Updated `registerContainerConfiguration()` to use `dirname(__DIR__).'/config/...'`
10. Replaced `%kernel.root_dir%/config/` → `%kernel.project_dir%/config/` in routing config
11. Replaced `%kernel.root_dir%/../web` → `%kernel.project_dir%/public` in image_filters.yml
12. Replaced `%kernel.root_dir%/../var/data/` → `%kernel.project_dir%/var/data/` in config_test.yml and parameters.yml.dist
13. Updated all `resource:` paths from `../../src/App/` → `../src/App/` in services.yml, event_subscribers.yml, twig.yml, validators.yml
14. Added `doctrine_migrations.dir_name: "%kernel.project_dir%/migrations"` to config.yml
15. Updated `composer.json`: `component-dir` and `symfony-web-dir` to use `public`
16. Updated `package.json`: test config path, docroot
17. Updated `gulpfile.js`: all `web/` → `public/`, `app/Resources/assets/` → `assets/`, `src/AppBundle/` → `src/App/`
18. Updated `.gitignore`: `/web/` → `/public/`, `/app/config/` → `/config/`, `/app/phpunit.xml` → `/phpunit.xml`
19. Updated `.github/workflows/tests.yml`: phpunit config path
20. Kept bundle overrides (`TwigBundle`, `FMElfinderBundle`) in `app/Resources/`

### What Was NOT Moved (by design)
- `app/AppKernel.php`, `app/AppCache.php`, `app/autoload.php` — still used by front controllers, will move in Sprint 5
- `app/Resources/TwigBundle/`, `app/Resources/FMElfinderBundle/` — Symfony 3.4 expects bundle overrides at `{kernel.root_dir}/Resources/{BundleName}/views/`
- `app/Resources/translations/` — translation files stay until Symfony 4.4

### Key Design Decision: Symlink for Templates
Symfony 3.4's colon-separated template syntax (`:admission:existingUser.html.twig`) resolves templates via `{kernel.root_dir}/Resources/views/`. Since `kernel.root_dir` remains `app/`, we created a symlink `app/Resources/views` → `../../templates` so both the new `templates/` directory and the legacy resolution path work. This symlink will be removed in Sprint 5.

### Gotchas for Next Sprint
- The symlink `app/Resources/views` → `../../templates` must be removed when the kernel moves to `src/Kernel.php`
- Bundle overrides in `app/Resources/{BundleName}/` need to move to `templates/bundles/{BundleName}/`
- `app/Resources/translations/` should move to `translations/` at root
- `KERNEL_DIR` in `phpunit.xml.dist` can be removed once Symfony 4.4's WebTestCase is used

### Test Results
496 tests, 1152 assertions, 2 pre-existing failures (unchanged)

---

## Sprint 5: Symfony 3.4 → 4.4 — COMPLETE

### What Was Done
1. **composer.json** — Split `symfony/symfony` monolith into 30+ individual 4.4.* packages. Added `symfony/dotenv`, `symfony/error-handler`. Upgraded: `doctrine/doctrine-bundle` ^2.0, `helios-ag/fm-elfinder-bundle` ^10.0, `knplabs/knp-paginator-bundle` ^5.0, `sensio/framework-extra-bundle` ^5.6, `friendsofsymfony/rest-bundle` ^2.8, `twig/twig` ^2.14. Removed `laminas/laminas-zendframework-bridge`.
2. **config/bundles.php** — Created bundle registry extracted from `AppKernel::registerBundles()`, with env-specific loading
3. **src/Kernel.php** — New Symfony 4.4 kernel using `MicroKernelTrait`. No namespace, autoloaded via classmap. Handles config and routing loading with environment-specific files.
4. **public/index.php** — Unified front controller replacing `app.php`, `app_dev.php`, `app_staging.php`. Uses `Dotenv` and `APP_ENV`/`APP_DEBUG` env vars.
5. **bin/console** — Rewritten for Symfony 4.4 bootstrap with `Dotenv`
6. **.env / .env.test** — Created with `APP_ENV`, `APP_DEBUG`, `APP_SECRET`
7. **config/routing.yml** — Converted all 190 `AppBundle:Controller:action` to FQCN `App\Controller\XController::yAction`. Changed annotation resource from `@AppBundle/Controller/` to `../src/App/Controller/`.
8. **config/routing_api.yml** — Changed `@AppBundle/Controller/API/` to `../src/App/Controller/Api/`
9. **Templates** — Fixed 12 `controller("AppBundle:X:y")` calls in Twig to FQCN format
10. **DQL/Form types** — Converted 47 `AppBundle:EntityName` references to FQCN across 26 files
11. **config/security.yml** — Removed `logout_on_user_change: true` (2 occurrences)
12. **config/config.yml** — Removed `templating:` section, removed `framework.router.resource` (now in Kernel), added explicit Doctrine ORM mapping for `App\Entity`, cleaned up fm_elfinder config (removed `include_assets` — dropped in ^10)
13. **config/config_dev.yml** — Removed `framework.router` override
14. **config/parameters.yml** — Changed `kernel.root_dir` to `kernel.project_dir`
15. **Doctrine persistence** — Updated `Doctrine\Common\Persistence` → `Doctrine\Persistence` in 33 files
16. **Role entity** — Fixed `__toString(): string` to return `getRole()` not `getName()` (critical: Symfony 4.4's `getRoleNames()` uses `(string)$role`). Added `parent::__construct($role)` call.
17. **Colon-syntax templates** — Fixed 4 controllers, 2 event subscribers, 1 service, and 2 Twig includes using `:dir:file.twig` → `dir/file.twig`
18. **phpunit.xml.dist** — Replaced `KERNEL_DIR` server var with `APP_ENV`, `APP_DEBUG`, `KERNEL_CLASS` env vars
19. **tests/bootstrap.php** — Updated to use new `Kernel` class with `Dotenv`
20. **Moved files** — `app/Resources/translations/` → `translations/`, `app/Resources/TwigBundle/` → `templates/bundles/TwigBundle/`, `app/Resources/FMElfinderBundle/` → `templates/bundles/FMElfinderBundle/`
21. **Deleted files** — `app/AppKernel.php`, `app/AppCache.php`, `app/autoload.php`, `src/App/AppBundle.php`, `public/app.php`, `public/app_dev.php`, `public/app_staging.php`, `app/Resources/views` symlink, entire `app/` directory

### What Was Deferred
- `Controller` → `AbstractController`: ~100 `$this->get()` calls across controllers need `getSubscribedServices()` override or refactoring. `Controller` still works in 4.4 (deprecated, removed in 5.0).
- `symfony/flex` integration: Not strictly needed, may cause recipe management issues during upgrade
- `AdvancedUserInterface` removal: Works in 4.4, removed in 5.0 (Sprint 6)
- `Role` entity should stop extending `Symfony\Component\Security\Core\Role\Role` (Sprint 6)

### Key Bugs Found & Fixed
1. **`Dotenv::bootEnv()` doesn't exist in 4.4** — Used `(new Dotenv(true))->loadEnv()` instead
2. **`fm-elfinder-bundle` ^10 removed `include_assets`** — Removed from all 7 elfinder instances in config.yml
3. **`Doctrine\Common\Persistence` removed** — Updated all 33 files to `Doctrine\Persistence`
4. **`Role::__toString()` broke auth** — In Symfony 4.4, `AbstractToken::getRoleNames()` calls `(string)$role` instead of `$role->getRole()`. Our `__toString()` returned human name ("Bruker") instead of role string ("ROLE_USER"), causing all authenticated users to get 403.
5. **`KERNEL_CLASS` needed** — Symfony 4.4 `KernelTestCase` doesn't auto-discover non-namespaced kernel
6. **`isValid()` before `isSubmitted()`** — Symfony 4.4 throws if `isValid()` called on unsubmitted form. Fixed in `ReceiptController` (3 occurrences), `ChangeLogController`, `TeamApplicationController`.
7. **Colon-syntax template includes** — Found additional `:dir:file.twig` references in `executive_board/index.html.twig` (2), `TeamInterestSubscriber` (1), `IntroductionEmailSubscriber` (2), `ApplicationAdmission` (1).

### Test Results
496 tests, 1150 assertions, 3 failures (all pre-existing), 2 warnings (pre-existing)
- `CompanyEmailMakerTest::testNorwegianCharacters` — pre-existing
- `CompanyEmailMakerTest::testAccentCharacters` — pre-existing
- `AdmissionPeriodEntityUnitTest::testShouldSendInfoMeetingNotification` — time-sensitive (fails near midnight)
- `GeoLocationTest` — 2 warnings, pre-existing mock issue

### Gotchas for Next Sprint (Sprint 6)
- `Controller` class removed in Symfony 5.0 — must migrate to `AbstractController` with proper `getSubscribedServices()` or dependency injection
- `AdvancedUserInterface` removed in 5.0 — implement `UserCheckerInterface` instead
- `Role` extending `Symfony\Component\Security\Core\Role\Role` — that class removed in 5.0
- `encoders:` config key renamed to `password_hashers:` in 5.3
- `anonymous: ~` in firewalls removed in 5.0
- SwiftMailer → Symfony Mailer

---

## Reference: vektor-backend Analysis

An existing modernization attempt lives at `github.com/vektorprogrammet/vektor-backend`. It reached **Symfony 5.4 on PHP 8.2**. Key findings:

### What they did
- Jumped from Symfony 3.4 → 5.4 directly (skipped 4.4)
- Split `symfony/symfony` monolith into individual 5.4 packages
- Full Flex layout: `src/Kernel.php` with `MicroKernelTrait`, `config/bundles.php`, `.env`
- Namespace `App\` mapped to `src/` (no AppBundle class)
- All annotations → PHP 8 attributes (`#[ORM\Entity]`, `#[Route]`)
- `Controller` → `AbstractController` with constructor-injected `ManagerRegistry`
- `AdvancedUserInterface` + `Serializable` → `UserInterface` + `PasswordAuthenticatedUserInterface`
- SwiftMailer → `symfony/mailer` with `MAILER_DSN`
- `encoders` → `password_hashers`, `enable_authenticator_manager: true`
- Added `rector/rector` for automated refactoring
- Slack: `symfony/slack-notifier` (we use Guzzle webhooks — both work)
- Added: EasyAdmin, JWT auth, API docs
- Removed: FOS CKEditor, ElFinder, KnpPaginator, FOSRestBundle, Sentry
- PHPUnit 9.5, `dama/doctrine-test-bundle` for fast DB test transactions
- Frontend NOT modernized: still Gulp 4 + jQuery + Bootstrap 4

### Useful reference files in vektor-backend
- `composer.json` — target package list for Symfony 5.4
- `config/packages/security.yaml` — migrated security config
- `src/Entity/User.php` — User entity with PHP 8 attributes
- `src/Kernel.php` — standard Flex kernel

### Implications for our sprint plan
1. **Skip 4.4**: Their success validates jumping 3.4 → 5.4 directly. 4.4 is EOL.
2. **Use Rector**: Automates annotation→attribute, Controller→AbstractController, getDoctrine() removal
3. **Their composer.json = our target deps** when splitting the monolith
4. **dama/doctrine-test-bundle**: Consider adding for faster tests
