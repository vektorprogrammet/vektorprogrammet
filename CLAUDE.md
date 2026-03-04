# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8.1. Norwegian university tutoring program: applications, interviews, teams, surveys, scheduling, admin.

## Quick Reference

| Command | Purpose |
|---------|---------|
| `composer test` | Full suite (536 tests, ~206s). Sets 256M memory limit. |
| `composer test:parallel` | ParaTest 4 workers (~103s) |
| `composer test:unit` | Unit tests only (<1s) |
| `composer analyse` | PHPStan level 1 |
| `composer lint` / `composer fix` | PHP-CS-Fixer check / apply |
| `npm run build:dev` / `build:prod` | Vite build |

## Architecture

- **Dual API**: API Platform 3.4 (`/api/*`, JWT) + FOS REST (`/api/party/*`, session). See `.claude/rules/php.md`.
- **Roles**: USER < TEAM_MEMBER < TEAM_LEADER < ADMIN (linear hierarchy)
- **Frontend**: Vite 5 (Bootstrap 4 + jQuery). v2 React SPA at `../v2/homepage/`.
- **40+ entities** across: users, departments, teams, applications, interviews, surveys, receipts, content
- **DTOs + State Processors**: `src/App/ApiResource/` + `src/App/State/` for all API Platform write operations

## Status

Sprints 1-9 + API Phase 1-2 + Frontend Integration + Admission Flow + Auth Profile + Password Reset: ALL COMPLETE.
Pending plans: Article CRUD, Public User Profile, Team Application, Existing User Readmission.

## Docs

| File | Topic |
|------|-------|
| `docs/architecture.md` | Controllers, roles, services, mailer, API Platform |
| `docs/conventions.md` | Code conventions and patterns |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/troubleshooting.md` | Error → fix lookup |
| `docs/overview.md` | Commands, structure, CI, test users |

## Rules (`.claude/rules/`)

| File | Scope | What it covers |
|------|-------|----------------|
| `php.md` | `src/**`, `config/**` | Constructor DI, PHP 8 attributes, API Platform patterns, FOS REST coexistence |
| `testing.md` | `tests/**` | Test commands, base classes, fixtures, known issues |
| `twig.md` | `templates/**` | Twig 3 syntax requirements |

## Agent Constraints

- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads).
- Never broad `replace_all` without verifying scope — check match count first.
- Test baseline in MEMORY.md. Never commit if new failures appear.

## Workflow

1. Read MEMORY.md + active plans. Check git status. Present status, wait for confirmation.
2. Code → verify per task. Commit per task.
3. End of session: commit work, update CLAUDE.md if workflow changed.
4. Context >50%: commit + write state before stopping.
