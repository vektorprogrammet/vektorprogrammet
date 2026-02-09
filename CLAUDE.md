# Vektorprogrammet Monolith

Norwegian tutoring program management platform. URLs in Norwegian. Upgrading Symfony 3.4 → 6.4. Details in `.planning/`.

## Sprints
- 1-7: COMPLETE (Symfony 6.4, PHP 8)
- 7b: COMPLETE — Fix 15 Sf6 test regressions (496 tests, 2 failures)
- 8-9: frontend, cleanup

## Testing

See **`docs/testing.md`** for full test workflow, suites, timing, and credentials.

```bash
bin/phpunit --testsuite=unit          # 183 tests, <1s
bin/phpunit --testsuite=controller    # 131 tests, ~110s
bin/phpunit --testsuite=availability  # 182 tests, ~67s
/usr/local/opt/php@8.4/bin/php $(which composer) [cmd] --no-scripts  # composer
```
**IMPORTANT**: Tests MUST run with `dangerouslyDisableSandbox: true`.
Use `run_in_background: true` for full suite (~3 min). Known failures: CompanyEmailMaker (2, locale).

## Architecture
- `BaseController` extends `AbstractController` with `getSubscribedServices()` for ~35 services + bridge `getDoctrine()`/`get()` methods
- Security: `ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`
- `User` implements `UserInterface` + `PasswordAuthenticatedUserInterface`
- `User::getRoles()` returns `string[]`; templates use `user.roleEntities` for entity access
- `Role::__toString()` returns `getRole()` not `getName()`
- Test credentials: `assistent/1234`, `teammember/1234`, `teamleader/1234`, `admin/1234`

## Workflow
**Start**: read `.planning/STATE.md`, run tests before changes, check `git status`/`git log`
**Plan**: risk-first. Define done-criteria per sprint. Never delete/redefine tasks mid-sprint.
**Dev**: small commits per logical change. Run smoke tests between changes. Sweep ALL file types when fixing a pattern.
**End**: update `.planning/STATE.md` and CLAUDE.md with any key decisions or changed commands. Leave tests passing. Record lessons in memory files.
**Docs**: after completing a task, review what changed — update CLAUDE.md and `.planning/` if commands, architecture, or decisions changed. Keep CLAUDE.md concise.
