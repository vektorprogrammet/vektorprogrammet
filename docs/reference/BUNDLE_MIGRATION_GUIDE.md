# Symfony Bundle to Laravel Migration Guide

This document provides detailed mapping of Symfony bundles to Laravel equivalents and migration strategies.

## Core Framework Bundles

### Symfony FrameworkBundle → Laravel Framework

**Migration:** Direct replacement with Laravel framework components.

### Symfony SecurityBundle → Laravel Auth + Gates/Policies

**Files to Migrate:**

- `app/config/security.yml` → `config/auth.php`, `app/Policies/`, `app/Http/Middleware/`

**Key Features:**

- User providers (chain provider with username/email/companyEmail) → Laravel user providers
- Role hierarchy → Laravel Gates/Policies with role inheritance
- Form login → Laravel login routes/controllers
- Remember me → Laravel "Remember Me" feature
- Access control rules → Laravel route middleware and Policies

**Migration Steps:**

1. Create Laravel User model implementing `Authenticatable`
2. Configure authentication in `config/auth.php`
3. Convert role hierarchy to Policy classes or Gate definitions
4. Convert access control rules to route middleware groups
5. Migrate login/logout controllers
6. Implement remember me functionality

### TwigBundle → Laravel Blade

**Files to Migrate:**

- `app/Resources/views/*` (264 templates)
- `app/config/twig.yml` → Laravel view configuration

**Migration Steps:**

1. Convert Twig syntax to Blade:
   - `{{ variable }}` → `{{ $variable }}`
   - `{% extends %}` → `@extends`
   - `{% include %}` → `@include`
   - `{% for %}` → `@foreach`
   - `{% if %}` → `@if`
2. Convert Twig functions to Blade directives or helpers
3. Convert Twig extensions → Blade service providers
4. Migrate template inheritance structure
5. Update template paths and naming

### MonologBundle → Laravel Logging

**Files to Migrate:**

- `app/config/config.yml` (monolog section)
- Custom `LogService` → Laravel Log facade or custom channel

**Migration:**

- Laravel has built-in logging with Monolog
- Configure channels in `config/logging.php`
- Custom LogService can wrap Laravel logging

## ORM & Database

### Doctrine Bundle → Laravel Eloquent (Recommended)

**Alternative:** Use Doctrine ORM bridge for Laravel

**Files to Migrate:**

- `app/config/config.yml` (doctrine section)
- All Entity classes → Eloquent Models
- Custom repositories → Laravel model methods or separate repositories

**Migration Strategy:**

1. **Option A - Eloquent (Recommended):**
   - Convert Doctrine entities to Eloquent models
   - Map relationships (ManyToOne → `belongsTo`, OneToMany → `hasMany`, etc.)
   - Convert custom repositories to model methods or Repository classes
   - Update all queries from DQL/QueryBuilder to Eloquent

2. **Option B - Doctrine Bridge:**
   - Use `laravel-doctrine/orm` package
   - Keep existing Doctrine entities with minimal changes
   - Easier migration but less "Laravel-native"

**Recommendation:** Use Eloquent for better Laravel integration, but requires more migration work.

### Doctrine Migrations Bundle → Laravel Migrations

**Files:**

- `app/DoctrineMigrations/*` (71 files) → `database/migrations/`

**Migration Steps:**

1. Convert each Doctrine migration to Laravel migration
2. Preserve migration order
3. Test migration rollback
4. Ensure data integrity

**Example Conversion:**

```php
// Doctrine
$this->addSql('CREATE TABLE users...');

// Laravel
Schema::create('users', function (Blueprint $table) {
    // ...
});
```

### Doctrine Fixtures Bundle → Laravel Seeders

**Files:**

- `src/AppBundle/DataFixtures/ORM/*` → `database/seeders/`

**Migration:**

- Convert fixture classes to Laravel Seeder classes
- Update `php artisan db:seed` usage

## REST API

### FOSRestBundle → Laravel API Resources

**Files:**

- `app/config/routing_api.yml`
- `src/AppBundle/Controller/API/*` → `app/Http/Controllers/Api/`
- REST controllers → Laravel API controllers with Resources

**Migration Steps:**

1. Convert REST route definitions to Laravel API routes
2. Create API Resource classes for serialization
3. Implement API authentication (tokens/keys)
4. Convert REST controllers to Laravel controllers
5. Use Laravel API Resources for response formatting

**Route Conversion:**

```yaml
# Symfony FOSRestBundle
sponsors:
    type: rest
    resource: "@AppBundle/Controller/API/SponsorController.php"
```

```php
// Laravel
Route::apiResource('sponsors', SponsorController::class);
```

## Third-Party Bundles

### Egeloen CKEditor Bundle → Laravel CKEditor

**Package:** `egeloen/ckeditor-bundle` (v6.0)

**Laravel Alternatives:**

- `ckeditor/ckeditor-laravel-package`
- `unisharp/laravel-ckeditor`
- Or use CKEditor directly with Laravel Mix

**Migration:**

- Replace bundle configuration with Laravel package
- Update asset compilation
- Migrate form types using CKEditor

### KNP Paginator Bundle → Laravel Pagination

**Package:** `knplabs/knp-paginator-bundle` (v2.4)

**Laravel:** Built-in pagination, no package needed

**Migration:**

```php
// Symfony
$paginator = $this->get('knp_paginator');
$pagination = $paginator->paginate($query, $page, $limit);

// Laravel
$items = Model::paginate($perPage);
// or
$items = Model::simplePaginate($perPage);
```

### FM Elfinder Bundle → Laravel File Manager

**Package:** `helios-ag/fm-elfinder-bundle` (v9.1)

**Laravel Alternatives:**

- `unisharp/laravel-filemanager`
- `alexusmai/laravel-file-manager`
- Or build custom file browser

**Migration:**

- Replace bundle with Laravel package
- Update file browser routes
- Migrate file upload handling

### Liip Imagine Bundle → Image Processing

**Package:** `liip/imagine-bundle` (v2.1)

**Laravel Alternatives:**

- `intervention/image` (most popular)
- `spatie/laravel-image`
- Or use `liip/imagine` directly (without bundle)

**Migration:**

```php
// Symfony
$imagine = $this->get('liip_imagine');
$filteredImage = $imagine->filter($imagePath, 'thumbnail');

// Laravel (with Intervention)
$img = Image::make($imagePath);
$img->resize(300, 300);
$img->save($outputPath);
```

### Nexy Slack Bundle → Laravel Slack

**Package:** `nexylan/slack-bundle` (v2.0)

**Laravel:** `laravel/slack-notification-channel` (built-in) or custom service

**Migration:**

```php
// Symfony
$slackMessenger = $this->get('nexy_slack.client');
$slackMessenger->send('message', '#channel');

// Laravel
use Illuminate\Support\Facades\Notification;
Notification::route('slack', '#channel')
    ->notify(new SlackNotification($message));
```

### EWZ Recaptcha Bundle → Laravel reCAPTCHA

**Package:** `excelwebzone/recaptcha-bundle` (v1.5)

**Laravel Alternatives:**

- `anhskohbo/no-captcha`
- `greggilbert/recaptcha`

**Migration:**

- Replace bundle with Laravel package
- Update form validation
- Migrate reCAPTCHA widget rendering

### Sentry Symfony → Sentry Laravel

**Package:** `sentry/sentry-symfony` (v2.0) → `sentry/sentry-laravel`

**Migration:**

1. Install `sentry/sentry-laravel`
2. Configure in `config/sentry.php`
3. Update environment variables
4. Update custom Sentry integrations

### BCC Auto Mapper Bundle → DTO Mapping

**Package:** `bcc/auto-mapper-bundle` (v1.2)

**Laravel Alternatives:**

- `spatie/data-transfer-object`
- Manual mapping
- Laravel API Resources (for API responses)

**Migration:**

- Evaluate if needed (may be overkill)
- Use Laravel API Resources for serialization
- Or implement manual DTO classes

## Email & Messaging

### Swiftmailer Bundle → Laravel Mail

**Package:** `symfony/swiftmailer-bundle` (v3.2.3)

**Laravel:** Built-in Mail system (supports multiple drivers)

**Migration:**

```php
// Symfony
$message = new Swift_Message();
$message->setFrom('from@example.com')
    ->setTo('to@example.com')
    ->setBody($body, 'text/html');
$this->get('mailer')->send($message);

// Laravel
Mail::to('to@example.com')
    ->send(new CustomMail($data));
```

**Custom Mailer Service:**

- Migrate `AppBundle\Mailer\Mailer` → Laravel Mail Mailable classes
- Or wrap Laravel Mail in service class for compatibility

## Custom Integrations

### Google APIs

**Custom Services:** `src/AppBundle/Google/*`

**Packages Used:** `google/apiclient` (v2.2)

**Laravel Migration:**

- Keep using `google/apiclient` package
- Convert services to Laravel service classes
- Use Laravel service providers for configuration
- Migrate service definitions from `services_google.yml`

### SMS Gateway

**Custom Services:** `src/AppBundle/Sms/*`

**Laravel Migration:**

- Convert to Laravel notification channel or service class
- Keep existing SMS gateway integration logic
- Wrap in Laravel service provider

### DomPDF

**Package:** `dompdf/dompdf` (v1.0)

**Laravel:** Same package works directly

**Migration:**

- No bundle needed, use package directly
- Create Laravel service or helper class

## Development & Testing Bundles

### Sensio Generator Bundle → Laravel Artisan

**Package:** `sensio/generator-bundle` (v3.1) - Dev only

**Laravel:** Built-in Artisan generators

**Migration:**

- Use `php artisan make:controller`, `make:model`, etc.
- No direct equivalent needed

### PHP CS Fixer → Laravel Pint (or PHP CS Fixer)

**Package:** `friendsofphp/php-cs-fixer` (v2.0) - Dev only

**Laravel:**

- Laravel Pint (built-in, based on PHP CS Fixer)
- Or continue using PHP CS Fixer

**Migration:**

- Use Laravel Pint, or
- Keep existing PHP CS Fixer configuration

### Nelmio CORS Bundle → Laravel CORS

**Package:** `nelmio/cors-bundle` (v1.5) - Dev only

**Laravel:** `fruitcake/laravel-cors` (Laravel 9+) or built-in CORS

**Migration:**

- Configure CORS in `config/cors.php`
- Laravel has built-in CORS support

## Configuration Migration Checklist

### Bundle Configuration Files to Migrate

1. **Security:**
   - `app/config/security.yml` → `config/auth.php`, `app/Policies/`, middleware

2. **Services:**
   - `app/config/services.yml` → `app/Providers/AppServiceProvider.php`
   - `app/config/services_google.yml` → `app/Providers/GoogleServiceProvider.php`
   - `app/config/event_subscribers.yml` → `app/Providers/EventServiceProvider.php`

3. **Doctrine:**
   - `app/config/config.yml` (doctrine section) → `config/database.php`

4. **Twig:**
   - `app/config/twig.yml` → `config/view.php`

5. **Forms:**
   - `app/config/forms.yml` → Form Request classes and validation

6. **Validators:**
   - `app/config/validators.yml` → Laravel validation rules

7. **Monolog:**
   - Monolog config → `config/logging.php`

## Migration Priority

### High Priority (Critical Functionality)

1. SecurityBundle (Authentication/Authorization)
2. Doctrine Bundle (Database/ORM)
3. Twig Bundle (Templates)
4. Security & Authentication related bundles

### Medium Priority (Core Features)

1. FOSRestBundle (API)
2. Swiftmailer (Email)
3. Monolog (Logging)
4. Doctrine Migrations

### Lower Priority (Can Be Done Later)

1. File manager (Elfinder)
2. Image processing (Imagine)
3. Development bundles
4. Optional features

## Package Compatibility Notes

### PHP Version

- Current: PHP >= 7.1
- Laravel 9+ requires PHP >= 8.0
- **Action Required:** Upgrade PHP before Laravel migration

### Composer Packages

- Most packages work in both Symfony and Laravel
- Some bundles are Symfony-specific and need Laravel equivalents
- Check package compatibility before migration

## Testing Migration

For each bundle migration:

1. Write tests before migration
2. Test equivalent functionality in Laravel
3. Compare output/results
4. Ensure no regression
