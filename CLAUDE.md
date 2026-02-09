# Vektorprogrammet Monolith

Norwegian tutoring program management platform. URLs in Norwegian. Upgrading Symfony 3.4 → 6.4. Details in `progress.md`.

## Sprints
- 1-5: COMPLETE (now on Symfony 4.4)
- 6: 4.4→5.4 — remove `AdvancedUserInterface`, SwiftMailer→Mailer, `encoders`→`password_hashers`, switch to PHP 8
- 7: 5.4→6.4 — annotations→attributes, inject repos instead of `getDoctrine()`
- 8-9: frontend, cleanup

## Commands (PHP 7.4 required until Sprint 6)
```bash
/usr/local/opt/php@7.4/bin/php                                          # php
/usr/local/opt/php@7.4/bin/php $(which composer) [cmd] --no-scripts --ignore-platform-req=composer-plugin-api  # composer
/usr/local/opt/php@7.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist   # tests (496, 3 pre-existing failures)
/usr/local/opt/php@7.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --filter="AvailabilityFunctionalTest|SecurityControllerTest"  # smoke test
```

## Architecture
- `BaseController` extends `Controller` not `AbstractController` (~100 `$this->get()` calls, deferred to Sprint 6)
- Security: `ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`. `User` has `AdvancedUserInterface` + `Serializable` (removed in Sf5)
- `Role::__toString()` returns `getRole()` not `getName()` — critical for Sf4.4 role resolution

## Workflow
**Start**: read `progress.md`, run tests before changes, check `git status`/`git log`
**Plan**: risk-first (architectural decisions before quick wins). Define explicit done-criteria per sprint. Never delete/redefine tasks mid-sprint.
**Dev**: small commits per logical change. Run smoke tests between changes. When fixing a pattern, sweep ALL file types (controllers, subscribers, services, templates).
**End**: update `progress.md`, leave tests passing, record lessons in memory files
