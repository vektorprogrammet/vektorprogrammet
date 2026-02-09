# Vektorprogrammet Monolith

Norwegian tutoring program management platform. Symfony 6.4, PHP 8. Upgrading from 3.4 → 6.4.

## Status
- Sprints 1-7b: COMPLETE (Sf6 upgrade + test regression fixes)
- Sprint 8-9: NEXT (DI migration, attributes, frontend)
- Details: `.planning/STATE.md`

## Docs
- **Testing**: `docs/testing.md` (commands, suites, credentials, file→test mapping)
- **Architecture**: `docs/architecture.md` (controllers, roles, services, mailer)

## Critical Gotchas
- Tests MUST run with `dangerouslyDisableSandbox: true` (SQLite + vendor reads)
- Known failures: 2 CompanyEmailMaker tests (macOS locale, pre-existing)
- HEREDOC in git commit fails in sandbox — use plain quoted strings

## Workflow
**Start**: read `.planning/STATE.md`, run tests, check `git status`/`git log`
**Dev**: small commits per logical change. Run `--filter=RelevantTest` between changes.
**End**: update STATE.md and CLAUDE.md if commands/architecture/decisions changed. Leave tests passing.
