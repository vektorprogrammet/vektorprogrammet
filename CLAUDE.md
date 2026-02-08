# Vektorprogrammet Monolith — AI Agent Context

## Project Overview
Norwegian educational tutoring program ("Vektorprogrammet") management platform. Features: admissions, interviews, team management, scheduling, surveys, user management, CMS. All URLs are in Norwegian (e.g., `/kontrollpanel`, `/opptak`, `/undersokelse`).

## Active Modernization: Symfony 3.4 → 6.4

We are incrementally upgrading this Symfony 3.4 monolith to Symfony 6.4. Track progress in `progress.md`. The full sprint plan is below.

### Current Sprint Status
- Sprint 1: COMPLETE (PR #1592, branch `modernize/sprint-1-remove-dead-dependencies`)
- Sprint 2+: PENDING — see sprint plan below

## Critical Environment Setup

### PHP Version
- **System PHP is 8.5.2** — incompatible with Symfony 3.4
- **Must use PHP 7.4** at `/usr/local/opt/php@7.4/bin/php` for all commands
- Switch to system PHP only after reaching Symfony 5.4+ (Sprint 6)

### Composer Commands
Always use these flags until elfinder-bundle is upgraded (Sprint 2):
```bash
/usr/local/opt/php@7.4/bin/php $(which composer) [command] --no-scripts --ignore-platform-req=composer-plugin-api
```

### Running Tests
```bash
/usr/local/opt/php@7.4/bin/php -d memory_limit=512M bin/phpunit -c app/phpunit.xml.dist
```
- Baseline: 496 tests, 1152 assertions
- 2 pre-existing failures in `CompanyEmailMakerTest` (Norwegian character handling) — ignore these

### Running Console
```bash
/usr/local/opt/php@7.4/bin/php bin/console [command]
```

## Codebase Scale

| Component | Count | Location |
|---|---|---|
| Entities | 91 | `src/AppBundle/Entity/` |
| Repositories | 36 | `src/AppBundle/Entity/Repository/` |
| Controllers | 62 | `src/AppBundle/Controller/` (incl. `Api/`) |
| Services | 33 | `src/AppBundle/Service/` |
| Form Types | 68 | `src/AppBundle/Form/` |
| Twig Templates | 264 | `app/Resources/views/` |
| Event Subscribers | 14 | `src/AppBundle/EventSubscriber/` |
| Twig Extensions | 12 | `src/AppBundle/Twig/Extension/` |
| Console Commands | 6 | `src/AppBundle/Command/` |
| Tests | 70 files, 496 tests | `tests/` |
| Routes | ~180 | `app/config/routing.yml` |
| Migrations | 71 | `app/DoctrineMigrations/` |

## Key Architectural Patterns

### BaseController (all admin controllers extend this)
`src/AppBundle/Controller/BaseController.php` extends `Controller` (not `AbstractController`). Provides:
- `getDepartment(Request)` — resolves department from query param or user
- `getSemester(Request)` — resolves semester from query param or current
- Uses `$this->getDoctrine()` throughout (deprecated in Symfony 4.4, removed in 6.0)

### Security Model (`app/config/security.yml`)
- 4 roles: `ROLE_USER` (assistant) < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`
- 3 user providers: username, email, companyEmail (chained)
- `User` entity implements `AdvancedUserInterface` + `Serializable` (both removed in Symfony 5/6)
- 85+ access control rules with Norwegian URL paths
- Password encoding: bcrypt, cost 12

### Services (`app/config/services.yml`)
- Autowiring enabled, all services public
- SlackMessenger now uses direct GuzzleHttp webhooks (rewritten in Sprint 1)
- Mailer binding: `MailerInterface` → `Mailer` (SwiftMailer-based)
- SMS binding: `SmsSenderInterface` → `SmsSender`

### External Integrations
- Google API (5 files in `src/AppBundle/Google/`): Gmail, Drive, Groups, Users
- Slack: Direct webhook via `SlackMessenger` (rewritten Sprint 1)
- SMS: GatewayAPI via `src/AppBundle/Sms/`
- Email: SwiftMailer (to be replaced with Symfony Mailer in Sprint 6)
- Sentry: Error tracking (prod/staging only)
- reCAPTCHA: Currently disabled (`ewz_recaptcha.enabled: false`)

### Frontend
- Gulp 4 + Babel 6 + node-sass (all EOL)
- Bootstrap 4.5 + CoreUI 2.0 + jQuery 3.5
- Separate React sub-app: `src/AppBundle/AssistantScheduling/Webapp/`
- Node.js 14.21.3 (EOL)

## Sprint Plan (Symfony 3.4 → 6.4)

### Sprint 1: Remove Dead Dependencies — COMPLETE
Removed: sensio/distribution-bundle, sensio/generator-bundle, bcc/auto-mapper-bundle, twig/extensions, incenteev/composer-parameter-handler, nexylan/slack-bundle, php-http/guzzle6-adapter. Rewrote Slack integration.

### Sprint 2: Replace Deprecated Bundles
- Replace `egeloen/ckeditor-bundle` → `friendsofsymfony/ckeditor-bundle`
- Update `knplabs/knp-paginator-bundle` to ^5.0
- Update `liip/imagine-bundle` to ^2.7
- Update `doctrine/doctrine-bundle` to ^2.0, `doctrine/doctrine-migrations-bundle` to ^3.0
- Update `friendsofsymfony/rest-bundle` to ^3.0
- Update `sentry/sentry-symfony` to ^4.0
- Replace `excelwebzone/recaptcha-bundle` with compatible version
- Update `helios-ag/fm-elfinder-bundle` to 10+ (requires Symfony 4, so may need to defer)

### Sprint 3: Namespace Rename (AppBundle → App)
Rename `AppBundle\` → `App\` across all ~300+ PHP files. One commit per category (entities, controllers, services, etc.). Move `src/AppBundle/` → `src/`.

### Sprint 4: Directory Restructure
`web/` → `public/`, `app/config/` → `config/`, `app/Resources/views/` → `templates/`. Rewrite kernel, add Flex, create `.env`.

### Sprint 5: Symfony 3.4 → 4.4
Replace `symfony/symfony` monolith with individual 4.4 packages. Fix `Controller` → `AbstractController`. Remove `logout_on_user_change`.

### Sprint 6: Symfony 4.4 → 5.4
Remove `AdvancedUserInterface`, build `UserChecker`. Replace SwiftMailer with Symfony Mailer. `encoders` → `password_hashers`. Can switch to system PHP 8.x after this.

### Sprint 7: Symfony 5.4 → 6.4
Remove `sensio/framework-extra-bundle`. Convert annotations → PHP 8 attributes (ORM + Route). Inject repos instead of `getDoctrine()`. Update PHPUnit to 10.x.

### Sprint 8: Frontend Modernization
Replace Gulp with Webpack Encore or Vite. Bootstrap 4 → 5. Node.js to LTS. Replace node-sass with dart-sass.

### Sprint 9: Cleanup
Update Dockerfile, CI/CD, README. Final test pass.

## Files You Should Read First
- `composer.json` — current dependency state
- `app/AppKernel.php` — registered bundles
- `app/config/security.yml` — auth model and access control
- `app/config/services.yml` — DI container config
- `app/config/config.yml` — framework and bundle config
- `src/AppBundle/Controller/BaseController.php` — base class for all controllers
- `src/AppBundle/Entity/User.php` — central entity
- `progress.md` — what's been done and decisions made
