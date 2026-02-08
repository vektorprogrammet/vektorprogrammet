# Vektorprogrammet Monolith — AI Agent Context

## Project Overview
Norwegian educational tutoring program management platform (admissions, interviews, teams, surveys, CMS). URLs are in Norwegian. Incrementally upgrading Symfony 3.4 → 6.4. See `progress.md` for detailed history.

## Sprint Status
- Sprints 1-5: COMPLETE (dead deps, deprecated bundles, namespace rename, directory restructure, Symfony 4.4)
- Sprint 6: Symfony 4.4 → 5.4 — Remove `AdvancedUserInterface`, SwiftMailer → Symfony Mailer, `encoders` → `password_hashers`, switch to PHP 8.x
- Sprint 7: Symfony 5.4 → 6.4 — annotations → PHP 8 attributes, inject repos instead of `getDoctrine()`
- Sprint 8-9: Frontend modernization, cleanup

## Environment Setup

**Must use PHP 7.4** (system PHP 8.5.2 is incompatible until Sprint 6):
```bash
# PHP
/usr/local/opt/php@7.4/bin/php

# Composer
/usr/local/opt/php@7.4/bin/php $(which composer) [command] --no-scripts --ignore-platform-req=composer-plugin-api

# Tests (496 tests, 2 pre-existing CompanyEmailMakerTest failures — ignore)
/usr/local/opt/php@7.4/bin/php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist

# Console
/usr/local/opt/php@7.4/bin/php bin/console [command]
```

## Key Architecture
- **BaseController** (`src/App/Controller/BaseController.php`): extends `Controller` (not yet `AbstractController` — ~100 `$this->get()` calls need refactoring first, deferred to Sprint 6)
- **Security**: 4 roles (`ROLE_USER` < `ROLE_TEAM_MEMBER` < `ROLE_TEAM_LEADER` < `ROLE_ADMIN`), `User` implements `AdvancedUserInterface` + `Serializable` (removed in Symfony 5/6)
- **Role entity**: `__toString()` returns `getRole()` (not `getName()`) — critical for Symfony 4.4 role resolution

## Key Files
`composer.json`, `src/Kernel.php`, `config/bundles.php`, `config/security.yml`, `config/services.yml`, `config/config.yml`, `src/App/Controller/BaseController.php`, `src/App/Entity/User.php`, `progress.md`
