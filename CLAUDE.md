# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8.5. Norwegian university tutoring program: applications, interviews, teams, surveys, scheduling, admin.

## Quick Reference

| Command | Purpose |
|---------|---------|
| `composer test` | Full suite (1001 tests, ~280s). Sets 256M — OOMs at ~640+ tests, use `php -d memory_limit=512M bin/phpunit` if needed. |
| `composer test:parallel` | ParaTest 4 workers (~67s) |
| `composer test:unit` | Unit tests only (<1s) |
| `composer analyse` | PHPStan level 1 |
| `composer lint` / `composer fix` | PHP-CS-Fixer check / apply |
| `npm run build:dev` / `build:prod` | Vite build |

## Architecture

- **API**: API Platform 3.4 (`/api/*`, JWT). See `.claude/rules/php.md`.
- **Roles**: USER < TEAM_MEMBER < TEAM_LEADER < ADMIN (linear hierarchy)
- **Frontend**: Vite 5 (Bootstrap 4 + jQuery). v2 React SPA at `../v2/homepage/`.
- **40+ entities** across: users, departments, teams, applications, interviews, surveys, receipts, content
- **DTOs + State Providers/Processors**: `src/App/ApiResource/` + `src/App/State/` for all API Platform operations

## Current Focus

API Migration complete: 93 API Platform endpoints across 6 waves (1030 tests, 3095 assertions). Staging environment built (Docker Compose + Caddy + GH Actions). Next: validate staging, then controller deprecation as API routes replace them. PR #1592 pending — main is production, needs staging validation before merge.

## Docs

| File | Topic |
|------|-------|
| `docs/architecture.md` | Controllers, roles, services, mailer, API Platform, rate limiting |
| `docs/conventions.md` | Code conventions and patterns |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/troubleshooting.md` | Error → fix lookup |
| `docs/staging-setup.md` | Staging droplet setup, deploy, reset |

## Rules (`.claude/rules/`)

| File | Scope | What it covers |
|------|-------|----------------|
| `php.md` | `src/**`, `config/**` | DI, attributes, API Platform, rate limiting, PHP 8.5 |
| `testing.md` | `tests/**` | Test commands, base classes, known issues |
| `twig.md` | `templates/**` | Twig 3 syntax requirements |

## Agent Constraints

- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads).
- Never broad `replace_all` without verifying scope — check match count first.
- Test baseline in MEMORY.md. Never commit if new failures appear.

## Workflow

1. Read MEMORY.md + active plans. Check git status. Present status, wait for user to specify task.
2. Code → verify per task. Commit per task.
3. End of session: commit work, update CLAUDE.md if workflow changed.
