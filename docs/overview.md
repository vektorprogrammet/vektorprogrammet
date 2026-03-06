# Developer Overview

Quick reference for working with the codebase. Each topic links to a dedicated doc for details.

## Composer Scripts

```bash
composer test                # Full test suite sequential (~3.5 min)
composer test:parallel       # Full test suite parallel -p4 (~103s)
composer test:coverage       # Full test suite with HTML coverage report (var/coverage/)
composer test:unit           # Unit tests only (<1s)
composer test:controller     # Controller tests (~110s)
composer test:availability   # Availability smoke tests (~67s)
composer lint                # Check code style (php-cs-fixer --dry-run)
composer fix                 # Auto-fix code style
composer analyse             # Static analysis (PHPStan level 1)
```

## Frontend Build (Vite)

```bash
npm run build:dev            # Build assets for development (unminified, with sourcemaps)
npm run build:prod           # Build assets for production (minified via terser + cssnano)
npm run watch                # Watch mode for development (rebuilds on file changes)
```

**Build Configuration**: `vite.config.js`

**Entry Points**:
- `assets/main.js` → `public/js/app.js` + `public/css/app.css`
- `assets/control-panel.js` → `public/js/controlPanel.js` + `public/css/control_panel.css`

**Output Directories**:
- `public/js/` - JavaScript bundles (app.js, controlPanel.js, vendor.js)
- `public/css/` - CSS bundles (app.css, control_panel.css)
- `public/vendor/` - Third-party libraries (CKEditor, Dropzone, CropperJS, CoreUI)
- `public/images/` - Copied from `assets/images/`
- `public/webfonts/` - FontAwesome webfonts

**Development Mode**:
- Unminified output for easier debugging
- Source maps enabled for JS and CSS
- Faster builds

**Production Mode**:
- Minified JS (terser) and CSS (cssnano)
- No source maps
- `console.*` statements removed from JS

**Vite** (Gulp fully replaced 2026-02-11)

## API (API Platform)

JSON API at `/api/*` for the v2 React homepage. JWT auth for protected endpoints, PUBLIC_ACCESS for homepage data.

**Read-only endpoints** (public): departments, teams, team_memberships, admission_periods, info_meetings, field_of_studies, statistics, articles, sponsors, static_contents, change_log_items, users (public profiles)

**Write endpoints** (public): `POST /api/applications`, `POST /api/applications/existing`, `POST /api/contact_messages`, `POST /api/admission_subscribers`, `POST /api/password_resets`, `POST /api/password_resets/{code}`, `POST /api/team_applications`

**Auth-required**: `GET/PUT /api/me` (profile), feedback

**Custom resources** (DTOs + state processors/providers):
- `Statistics` — aggregates user/assistant counts
- `ApplicationInput` — creates User + Application, dispatches event
- `ContactMessageInput` — sends email to department
- `AdmissionSubscriberInput` — idempotent subscriber creation
- `ProfileResource` — authenticated user profile (GET/PUT)
- `PasswordResetRequest` / `PasswordResetExecute` — password reset flow
- `TeamApplicationInput` — team application submission
- `ExistingUserReadmissionInput` — existing user re-admission
- `ArticleResource` — article CRUD (planned)

More: [architecture.md](architecture.md#api-platform)

## Project Structure

```
src/App/
  Controller/     # ~61 controllers (constructor DI, #[Route] attributes)
  Entity/         # Doctrine entities (PHP 8 #[ORM\...] attributes)
  ApiResource/    # API Platform DTOs (Statistics, ApplicationInput, ProfileResource, etc.)
  State/          # API Platform state providers/processors
  Service/        # Business logic services
  Role/           # Role hierarchy
  EventSubscriber/# Kernel event subscribers
  Twig/           # Twig extensions
  Command/        # Console commands
templates/        # Twig templates
config/           # Symfony config (YAML for services/packages, routes via attributes)
tests/AppBundle/  # PHPUnit tests
  Api/            # API endpoint tests (contract + functional)
docs/             # Developer documentation (modular, single-topic files)
```

## Architecture Highlights

- `BaseController` extends `AbstractController` — all controllers use constructor DI
- Routing via `#[Route]` PHP 8 attributes on controllers (only 3 third-party/firewall routes in YAML)
- Entities mapped with `#[ORM\...]` PHP 8 attributes (no annotations)
- Role hierarchy: `ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`
- `AccessControlService` manages route-level access rules with lazy-loaded cache
- URLs are in Norwegian (e.g., `/kontrollpanel/utlegg`, `/opptak`)

More: [architecture.md](architecture.md)

## CI

GitHub Actions (`.github/workflows/ci.yml`) runs on push to `master` and all PRs:

| Job | Command | What it checks |
|-----|---------|----------------|
| **Lint** | `composer lint` | Code style (PHP-CS-Fixer) |
| **Analyse** | `composer analyse` | Static analysis (PHPStan) |
| **Test** | `composer test` | Full PHPUnit test suite |

## Test Users (dev/test fixtures)

See [testing-details.md](testing-details.md) for test credentials.

## Further Reading

- [conventions.md](conventions.md) — Code conventions and patterns
- [troubleshooting.md](troubleshooting.md) — Error → fix lookup
- [knowledge-workflow.md](knowledge-workflow.md) — How insights flow from staging → docs
- [testing.md](testing.md) — Test commands, workflow, environment
- [testing-details.md](testing-details.md) — File-to-test mapping, timing, DB internals
- [architecture.md](architecture.md) — Controllers, roles, services, mailer
- [console-commands.md](console-commands.md) — Useful Symfony console commands
