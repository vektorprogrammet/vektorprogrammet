# Developer Overview

Quick reference for working with the codebase. Each topic links to a dedicated doc for details.

## Composer Scripts

```bash
composer test                # Full test suite sequential (496 tests, ~193s)
composer test:parallel       # Full test suite parallel -p4 (496 tests, ~103s)
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
  Controller/     # ~61 controllers (constructor DI, #[Route] attributes)
  Entity/         # Doctrine entities (PHP 8 #[ORM\...] attributes)
  Service/        # Business logic services
  Role/           # Role hierarchy
  EventSubscriber/# Kernel event subscribers
  Twig/           # Twig extensions
  Command/        # Console commands
templates/        # Twig templates
config/           # Symfony config (YAML for services/packages, routes via attributes)
tests/AppBundle/  # PHPUnit tests
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
