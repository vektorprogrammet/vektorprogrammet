# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8 platform. Norwegian tutoring program management.

## Status
- Sprints 1-8: COMPLETE (Sf6 upgrade, constructor DI, PHP 8 attributes)
- Sprint 9: COMPLETE (2026-02-11) — see MEMORY.md for final status

## Docs
| File | Topic |
|------|-------|
| `docs/overview.md` | Commands, structure, CI, test users |
| `docs/conventions.md` | Code conventions and patterns |
| `docs/troubleshooting.md` | Error → fix lookup |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/architecture.md` | Controllers, roles, services, mailer |

## Agent Gotchas
- Tests: `dangerouslyDisableSandbox: true` always (SQLite + vendor reads)
- `composer test` not raw `php bin/phpunit` (sets 256M memory)
- Test baseline: see MEMORY.md. Never commit if new failures appear.
- Never broad `replace_all` without verifying scope.

## Workflow
- **Start**: Read MEMORY.md, active plans, check git/tests, present status, wait for confirmation
- **Dev**: coding agent -> verify agent per task. Commit per task.
- **End**: Commit work, `/capture` insights, update CLAUDE.md if workflow changed.
- Context >50%: commit + write state before stopping.
