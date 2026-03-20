# Vektorprogrammet Server

Symfony 6.4 / PHP 8.5. Norwegian university tutoring program: applications, interviews, teams, surveys, scheduling, admin.

## Quick Reference

| Command | Purpose |
|---------|---------|
| `composer test` | Full suite (1001 tests, ~280s). OOMs at ~640+ tests — use `php -d memory_limit=512M bin/phpunit` if needed. |
| `composer test:parallel` | ParaTest 4 workers (~67s) |
| `composer test:unit` | Unit tests only (<1s) |
| `composer analyse` | PHPStan level 5 + strict rules (124 violations baselined) |
| `composer deptrac` | deptrac — DDD architecture boundary enforcement |
| `composer syntax` | php-parallel-lint — fast parse error check |
| `composer lint` / `composer fix` | PHP-CS-Fixer check / apply |

**Always clear cache before running tests** after namespace or config changes:
```bash
php bin/console cache:clear && php -d memory_limit=512M bin/phpunit
```

## Architecture

**Guiding principles:** Functional Core, Imperative Shell (FCIS) + Domain-Driven Design (DDD).

- **Functional Core** (`Domain/`): Pure PHP — business rules, value objects, event definitions, repository interfaces. Zero Symfony/Doctrine imports.
- **Imperative Shell** (`Infrastructure/`, `Api/`): Doctrine entities, repositories, API Platform resources/state, mailers, external integrations.
- **Doctrine entities stay as Infrastructure** — no entity splitting. The domain layer holds extracted logic (rules, computations), not aggregate root mirrors.

### Bounded Contexts

Code is organized by domain, not by technical layer:

| Context | Responsibility |
|---------|---------------|
| `Admission/` | Application lifecycle — submit, review, accept/reject |
| `Interview/` | Interview scheduling, conducting, scoring |
| `Organization/` | Departments, teams, boards, positions, user groups |
| `Survey/` | Questionnaire creation, distribution, responses |
| `Identity/` | Users, authentication, roles, access control |
| `Scheduling/` | Assistant-to-school assignment |
| `Operations/` | Receipts, certificates, assistant work history |
| `Content/` | Articles, static pages, sponsors, changelog, feedback |
| `Shared/` | Cross-context: Semester, interfaces |
| `Support/` | Context-agnostic infrastructure: mailer, SMS, Google, utilities |

### Layer Structure per Context

```
{Context}/
  Domain/Rules/          # Pure business rules (zero framework deps)
  Domain/ValueObjects/   # Immutable value types
  Domain/Events/         # Domain event definitions
  Domain/Contracts/      # Repository interfaces
  Infrastructure/Entity/ # Doctrine entities
  Infrastructure/Repository/
  Infrastructure/Subscriber/
  Api/Resource/          # API Platform DTOs
  Api/State/             # Providers + Processors
  Controller/            # Legacy Twig (deprecated)
  Form/                  # Legacy form types (deprecated)
```

### Cross-Context Dependencies

- **Infrastructure** layers may import across contexts (Doctrine requires this).
- **Domain** layers never import across contexts.
- **Department** owned by Organization, referenced by ID elsewhere.
- **User** owned by Identity, referenced by ID elsewhere.

### Key Technical Details

- **API**: API Platform 3.4 (`/api/*`, JWT). 93 endpoints across 6 domains.
- **Roles**: `ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN` (linear hierarchy)
- **Frontend**: Legacy Vite 5 (Bootstrap 4 + jQuery). New React apps in `apps/homepage/` and `apps/dashboard/`.
- **DTOs + State Providers/Processors** for all API Platform operations.

## Current Focus

API migration complete (93 endpoints, 1030 tests). DDD restructure planned — see specs and plans below. Staging environment pending validation (PR #1592).

## Docs

| File | Topic |
|------|-------|
| `docs/architecture.md` | Controllers, roles, services, mailer, API Platform, rate limiting |
| `docs/conventions.md` | Code conventions, FCIS principles, DDD patterns |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/troubleshooting.md` | Error → fix lookup |

### Specs & Plans

| File | Topic |
|------|-------|
| `../../docs/superpowers/specs/2026-03-20-ddd-fcis-analysis-design.md` | DDD end-state spec — 546 files mapped to bounded contexts |
| `../../docs/superpowers/plans/2026-03-20-ddd-restructure.md` | Implementation plan — 12 tasks, atomic commits per context |

## Rules (`.claude/rules/`)

| File | Scope | What it covers |
|------|-------|----------------|
| `php.md` | `src/**`, `config/**` | DI, attributes, API Platform, DDD namespace rules, PHP 8.5 |
| `testing.md` | `tests/**` | Test commands, base classes, known issues |
| `twig.md` | `templates/**` | Twig 3 syntax requirements |

## Agent Constraints

- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads).
- Never broad `replace_all` without verifying scope — check match count first.
- Always clear cache before running tests after moving files or changing config.
