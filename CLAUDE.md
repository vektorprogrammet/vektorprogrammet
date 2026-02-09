# Vektorprogrammet Monolith

Norwegian tutoring program management platform. Symfony 6.4, PHP 8. Upgrading from 3.4 → 6.4.

## Status
- Sprints 1-7b: COMPLETE (Sf6 upgrade + test regression fixes)
- Sprint 8-9: NEXT (DI migration, attributes, frontend)
- Details: `.planning/STATE.md`

## Docs
Each file covers one topic. Load only what you need to keep context small.

| File | Topic |
|------|-------|
| `docs/overview.md` | Quick reference: commands, structure, CI, test users |
| `docs/testing.md` | Test commands, workflow, environment |
| `docs/testing-details.md` | File-to-test map, timing, DB internals |
| `docs/architecture.md` | Controllers, roles, services, mailer |
| `docs/console-commands.md` | Useful Symfony console commands |
| `.github/workflows/ci.yml` | CI config (lint + analyse + test) |

**`docs/`** = for both humans and AI agents. **`.planning/`** = for AI agents only (migration state, task plans).

## Critical Gotchas
- Tests MUST run with `dangerouslyDisableSandbox: true` (SQLite + vendor reads)
- All 496 tests pass (0 known failures)
- HEREDOC in git commit fails in sandbox — use plain quoted strings

## Workflow
**Start**: read `.planning/STATE.md`, run tests, check `git status`/`git log`
**Dev**: small commits per logical change. Run `--filter=RelevantTest` between changes.
**End**: update STATE.md and CLAUDE.md if commands/architecture/decisions changed. Leave tests passing.
