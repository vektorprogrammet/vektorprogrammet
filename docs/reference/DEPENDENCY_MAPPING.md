# Dependency Mapping: Symfony to Laravel

This document maps all composer dependencies and their migration strategy.

## PHP Version Requirement

**Current:** PHP >= 7.1  
**Laravel 10+:** PHP >= 8.1  
**Action Required:** Upgrade PHP to 8.1+ before Laravel migration

## Core Framework Dependencies

### Symfony Components

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `symfony/symfony` | 3.4.* | Laravel Framework | Full framework replacement |
| `symfony/monolog-bundle` | ^3.1.0 | Built-in logging | Laravel uses Monolog internally |
| `symfony/swiftmailer-bundle` | ^3.2.3 | Laravel Mail | Built-in mail system |
| `symfony/polyfill-apcu` | ^1.0 | Not needed | Laravel handles this |

**Migration:** Remove all Symfony packages, use Laravel equivalents.

## ORM & Database

### Doctrine

| Package | Version | Laravel Equivalent | Migration Strategy |
|---------|---------|-------------------|-------------------|
| `doctrine/doctrine-bundle` | ^1.6 | Laravel Eloquent | **Option 1:** Convert to Eloquent (recommended)<br>**Option 2:** Use `laravel-doctrine/orm` bridge |
| `doctrine/orm` | ^2.5 | Laravel Eloquent | Convert entities to Eloquent models |
| `doctrine/dbal` | ~2.8 | `illuminate/database` | Keep for schema operations if needed |
| `doctrine/doctrine-migrations-bundle` | ^1.2 | Laravel Migrations | Convert 71 migrations to Laravel format |
| `doctrine/doctrine-fixtures-bundle` | ^3.0.2 | Laravel Seeders | Convert fixtures to seeders |

**Recommendation:** Convert to Eloquent for better Laravel integration, though more migration work.

## Templating

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `twig/twig` | ^2.0 | Laravel Blade | Convert 264 Twig templates to Blade |
| `twig/extensions` | ~1.0 | Blade directives | Create custom Blade directives |

## REST API

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `friendsofsymfony/rest-bundle` | ^2.5 | Laravel API Resources | Convert REST controllers to Laravel API controllers |
| `symfony/serializer` | 3.4.* | Laravel API Resources | Use Laravel's built-in serialization |

## Third-Party UI Bundles

| Package | Version | Laravel Package | Migration Notes |
|---------|---------|---------------|-----------------|
| `egeloen/ckeditor-bundle` | ~6.0 | `ckeditor/ckeditor-laravel-package`<br>or direct integration | Replace bundle with Laravel package |
| `knplabs/knp-paginator-bundle` | ~2.4 | Built-in pagination | Laravel has built-in pagination |
| `helios-ag/fm-elfinder-bundle` | ^9.1 | `unisharp/laravel-filemanager`<br>or `alexusmai/laravel-file-manager` | Replace with Laravel file manager |
| `liip/imagine-bundle` | ^2.1 | `intervention/image` | Use Intervention Image or `liip/imagine` directly |

## External Service Integrations

| Package | Version | Laravel Package | Migration Notes |
|---------|---------|---------------|-----------------|
| `nexylan/slack-bundle` | ^2.0 | `laravel/slack-notification-channel`<br>(built-in) | Use Laravel notification system |
| `excelwebzone/recaptcha-bundle` | ^1.5 | `anhskohbo/no-captcha`<br>or `greggilbert/recaptcha` | Replace with Laravel reCAPTCHA package |
| `sentry/sentry-symfony` | ^2.0 | `sentry/sentry-laravel` | Official Laravel Sentry package |
| `google/apiclient` | ^2.2 | Same package | Works directly in Laravel, wrap in service |

## Utility Packages

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `ramsey/uuid` | ^3.7 | Same package<br>or Laravel's Str::uuid() | Keep using package or use Laravel helper |
| `dompdf/dompdf` | ^1.0 | Same package | Works directly, no bundle needed |
| `bcc/auto-mapper-bundle` | ^1.2 | Manual mapping<br>or `spatie/data-transfer-object` | Evaluate necessity, may use Laravel API Resources |

## HTTP & Communication

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `php-http/guzzle6-adapter` | ^1.1 | Laravel HTTP Client<br>(Guzzle wrapper) | Use Laravel's HTTP facade |
| `laminas/laminas-zendframework-bridge` | ^1.4 | Not needed | Remove if not used |

## Development Dependencies

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `phpunit/phpunit` | ^7.4 | PHPUnit 9+ (Laravel 9+)<br>or PHPUnit 10+ (Laravel 10+) | Upgrade PHPUnit for Laravel compatibility |
| `friendsofphp/php-cs-fixer` | ^2.0 | Laravel Pint<br>or continue using PHP CS Fixer | Laravel Pint is built-in, or keep existing setup |
| `sensio/generator-bundle` | ~3.1 | Laravel Artisan | Use `php artisan make:*` commands |
| `nelmio/cors-bundle` | ^1.5 | Built-in CORS<br>or `fruitcake/laravel-cors` | Laravel has built-in CORS support |

## Configuration & Parameter Handling

| Package | Version | Laravel Equivalent | Migration Notes |
|---------|---------|-------------------|-----------------|
| `incenteev/composer-parameter-handler` | ^2.0 | Laravel `.env` files | Laravel uses `.env` for configuration |
| `sensio/distribution-bundle` | ^5.0.19 | Not needed | Laravel handles distribution |

## Migration Strategy by Package

### Keep As-Is (No Migration Needed)

- `ramsey/uuid` - Works in Laravel
- `dompdf/dompdf` - Works in Laravel
- `google/apiclient` - Works in Laravel (wrap in service)

### Replace with Laravel Built-in

- `symfony/symfony` → Laravel Framework
- `symfony/swiftmailer-bundle` → Laravel Mail
- `symfony/monolog-bundle` → Laravel Logging (uses Monolog internally)
- `knplabs/knp-paginator-bundle` → Laravel Pagination
- `friendsofsymfony/rest-bundle` → Laravel API Resources
- `symfony/serializer` → Laravel API Resources

### Replace with Laravel Packages

- `egeloen/ckeditor-bundle` → `ckeditor/ckeditor-laravel-package`
- `helios-ag/fm-elfinder-bundle` → `unisharp/laravel-filemanager`
- `liip/imagine-bundle` → `intervention/image`
- `excelwebzone/recaptcha-bundle` → `anhskohbo/no-captcha`
- `sentry/sentry-symfony` → `sentry/sentry-laravel`
- `nexylan/slack-bundle` → Laravel notifications (built-in)

### Convert/Migrate

- `doctrine/*` → Laravel Eloquent or `laravel-doctrine/orm`
- `twig/twig` → Laravel Blade (template conversion)
- All Symfony bundles → Laravel equivalents

### Remove (Not Needed)

- `incenteev/composer-parameter-handler` - Use `.env`
- `sensio/distribution-bundle` - Not needed
- `sensio/generator-bundle` - Use Artisan
- `nelmio/cors-bundle` - Built-in CORS
- `symfony/polyfill-apcu` - Not needed

## Composer.json Migration Checklist

### Remove from `require`

- [ ] `symfony/symfony`
- [ ] `symfony/monolog-bundle`
- [ ] `symfony/swiftmailer-bundle`
- [ ] `symfony/polyfill-apcu`
- [ ] `doctrine/doctrine-bundle` (if using Eloquent)
- [ ] `doctrine/orm` (if using Eloquent)
- [ ] `doctrine/dbal` (if not needed)
- [ ] `doctrine/doctrine-migrations-bundle`
- [ ] `doctrine/doctrine-fixtures-bundle`
- [ ] `twig/twig`
- [ ] `twig/extensions`
- [ ] `friendsofsymfony/rest-bundle`
- [ ] `symfony/serializer` (unless using directly)
- [ ] `egeloen/ckeditor-bundle`
- [ ] `knplabs/knp-paginator-bundle`
- [ ] `helios-ag/fm-elfinder-bundle`
- [ ] `liip/imagine-bundle`
- [ ] `nexylan/slack-bundle`
- [ ] `excelwebzone/recaptcha-bundle`
- [ ] `sentry/sentry-symfony`
- [ ] `bcc/auto-mapper-bundle`
- [ ] `incenteev/composer-parameter-handler`
- [ ] `sensio/distribution-bundle`
- [ ] `php-http/guzzle6-adapter` (use Laravel HTTP)
- [ ] `laminas/laminas-zendframework-bridge` (if not needed)

### Add to `require`

- [ ] `laravel/framework` (version compatible with PHP 8.1+)
- [ ] `intervention/image` (for image processing)
- [ ] `sentry/sentry-laravel` (error tracking)
- [ ] Laravel-specific packages as needed

### Keep (Works in Laravel)

- [ ] `ramsey/uuid` (or use Laravel Str::uuid())
- [ ] `dompdf/dompdf`
- [ ] `google/apiclient`

### Remove from `require-dev`

- [ ] `sensio/generator-bundle`
- [ ] `nelmio/cors-bundle` (unless using in dev)

### Update in `require-dev`

- [ ] `phpunit/phpunit` - Upgrade to PHPUnit 9+ or 10+ (depending on Laravel version)

### Keep in `require-dev`

- [ ] `friendsofphp/php-cs-fixer` (or switch to Laravel Pint)

## Package Version Compatibility

### PHP Version Upgrade Path

1. Current: PHP 7.1+ (Symfony 3.4 requirement)
2. Laravel 9: PHP 8.0+
3. Laravel 10: PHP 8.1+
4. **Recommendation:** Use Laravel 10 with PHP 8.1+ for long-term support

### PHPUnit Compatibility

- Symfony 3.4: PHPUnit 7.4
- Laravel 9: PHPUnit 9.x
- Laravel 10: PHPUnit 10.x
- **Action:** Upgrade PHPUnit with Laravel migration

## Dependency Resolution Notes

### Potential Conflicts

1. **Doctrine ORM** - If keeping Doctrine, use `laravel-doctrine/orm` bridge
2. **Twig** - Must fully convert to Blade (can't mix)
3. **Symfony Serializer** - Replace with Laravel API Resources
4. **Guzzle** - Laravel wraps Guzzle, use HTTP facade instead

### Testing Strategy

- Run `composer update` after each major package replacement
- Test functionality after each package migration
- Use feature parity tests to ensure no regression
