# Developer Overview

Quick reference for working with the codebase. Each topic links to a dedicated doc for details.

## Composer Scripts

```bash
composer test                # Full test suite (496 tests, ~3 min)
composer test:unit           # Unit tests only (<1s)
composer test:controller     # Controller tests (~110s)
composer test:availability   # Availability smoke tests (~67s)
composer lint                # Check code style (php-cs-fixer --dry-run)
composer fix                 # Auto-fix code style
composer analyse             # Static analysis (PHPStan level 1)
```

## Project Structure

```
src/App/
  Controller/     # ~55 controllers extending BaseController
  Entity/         # Doctrine entities (annotation-mapped)
  Service/        # Business logic services
  Role/           # Role hierarchy
  EventSubscriber/# Kernel event subscribers
  Twig/           # Twig extensions
  Command/        # Console commands
templates/        # Twig templates
config/           # Symfony config (YAML)
tests/AppBundle/  # PHPUnit tests
docs/             # Developer documentation (modular, single-topic files)
.planning/        # Migration state and plans (for AI coding agents)
```

## Architecture Highlights

- `BaseController` extends `AbstractController` with bridge methods for `getDoctrine()` and `get()` (pending migration to constructor DI)
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

| Role | Username | Password |
|------|----------|----------|
| Assistent | `assistent` | `1234` |
| Teammedlem | `teammember` | `1234` |
| Teamleder | `teamleader` | `1234` |
| Admin | `admin` | `1234` |

## Further Reading

- [testing.md](testing.md) — Test commands, workflow, environment
- [testing-details.md](testing-details.md) — File-to-test mapping, timing, DB internals
- [architecture.md](architecture.md) — Controllers, roles, services, mailer
- [console-commands.md](console-commands.md) — Useful Symfony console commands
