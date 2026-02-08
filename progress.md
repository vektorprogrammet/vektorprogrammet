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

## Sprint 3: Namespace Rename — NOT STARTED
See `CLAUDE.md` for sprint plan details.
