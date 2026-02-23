# Vektorprogrammet Monolith

Symfony 6.4 / PHP 8 platform. Norwegian tutoring program management.

## Status
- Sprints 1-8: COMPLETE (Sf6 upgrade, constructor DI, PHP 8 attributes)
- Sprint 9: COMPLETE (2026-02-11) — see MEMORY.md for final status
- API Platform Phase 1-2: COMPLETE (2026-02-19) — API Platform 3.4 + JWT auth, 11 read endpoints + 3 write endpoints
- v2 Frontend Integration: COMPLETE (2026-02-19) — homepage wired to real API (statistics, teams, kontakt, contact form)
- Admission Flow Migration: COMPLETE (2026-02-23) — backend subscriber endpoint + v2 admission page wired to API
- Auth Profile API: COMPLETE (2026-02-23) — GET/PUT /api/me (first auth-required endpoint)
- Password Reset API: COMPLETE (2026-02-23) — POST /api/password_resets + /api/password_resets/{code}

## Docs
| File | Topic |
|------|-------|
| `docs/overview.md` | Commands, structure, CI, test users |
| `docs/conventions.md` | Code conventions and patterns |
| `docs/troubleshooting.md` | Error → fix lookup |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/architecture.md` | Controllers, roles, services, mailer, API Platform |
| `docs/plans/2026-02-19-frontend-migration-design.md` | Frontend migration strategy (API + SPA) |
| `docs/plans/2026-02-19-api-platform-phase1.md` | API Platform Phase 1 plan |
| `docs/plans/2026-02-19-homepage-api-phase2.md` | Homepage API Phase 2 plan |
| `docs/plans/2026-02-23-admission-flow-design.md` | Admission flow migration design |
| `docs/plans/2026-02-23-admission-flow.md` | Admission flow implementation plan |
| `docs/plans/2026-02-23-auth-profile-api-design.md` | Auth profile API design |
| `docs/plans/2026-02-23-auth-profile-api.md` | Auth profile API implementation plan |

component boundaries in `varp.yaml`

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
