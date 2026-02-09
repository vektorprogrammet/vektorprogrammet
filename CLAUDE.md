# Vektorprogrammet Monolith

Norwegian tutoring program management platform. URLs in Norwegian. Upgrading Symfony 3.4 → 6.4. Details in `.planning/`.

## Sprints
- 1-6: COMPLETE (Symfony 5.4, PHP 8)
- 7: 5.4→6.4 — SwiftMailer→Mailer, annotations→attributes, getDoctrine()→DI
- 8-9: frontend, cleanup

## Commands
```bash
/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist   # tests (496, 14 known failures)
/usr/local/opt/php@8.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --filter="SorterTest"  # quick smoke
/usr/local/opt/php@8.4/bin/php $(which composer) [cmd] --no-scripts  # composer
```
Clear cache if service config changes: `rm -rf var/cache/test/`

## Known Test Failures (14)
- AccessRule (5): upstream deleted feature, routes 404
- Receipt (2): null asset in test data
- Interview/Survey template (3): "already rendered" in repeatable_question.html.twig
- CompanyEmailMaker (2): macOS missing `nb_NO` locale
- PasswordReset (1): deferred to Sprint 7 (SwiftMailer→Mailer)

## Architecture
- `BaseController` extends `AbstractController` with `getSubscribedServices()` for ~35 services
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
