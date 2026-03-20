# Software Developer Agent Memory

> For PHP conventions, invoke the `php-conventions` skill.
> For testing, invoke the `symfony-testing` skill.
> For namespace migration, invoke the `ddd-namespace-migration` skill.

## Config Architecture

- `.env` committed (Symfony standard). `.env.local` gitignored for secrets.
- JWT passphrase in `.env` is `dev_jwt_passphrase` — matches PEM keys in `config/jwt/`.
- Static params stay as YAML in services.yaml; environment-varying use `%env()%`.
- Dev SQLite path in `config/packages/dev/doctrine.yaml` (DotEnv can't resolve `%kernel.project_dir%`).

## Docker / Composer

- Dockerfile uses `php:8.4-cli` (8.5 image not yet published), Bun for frontend.
- `google/apiclient-services` trimmed from 130MB → 2MB via `pre-autoload-dump` cleanup. After editing `extra` list: `rm -rf vendor/google/apiclient-services && composer install`.
- `COMPOSER_PROCESS_TIMEOUT=600` in Docker (VirtioFS I/O slow on macOS).

## Entity Gotchas

- `User::setPassword()` internally calls `password_hash(BCRYPT, cost=12)` — do NOT use UserPasswordHasherInterface (double-hash).
- Username field is `user_name` (underscore), not `userName`.
- ManyToMany replacement: `$entity->getCollection()->clear()` before adding new.
- AdmissionPeriod delete must cascade InfoMeeting (remove InfoMeeting BEFORE — FK constraint).
- ExecutiveBoard is singleton — `findBoard()` returns the single entity.

## API Platform Edge Cases

- PUT+DELETE on same URI template on same class = 405. Use separate resource classes.
- POST+{id}: do NOT use `read: false` — prevents provider call. Use `deserialize: false` only.
- DELETE on standalone class MUST have a provider — `read: false` alone causes "Could not resolve argument $data".
- Provider runs BEFORE security — 404 takes priority over 403.
- JSON serialization omits null fields — don't `assertArrayHasKey` on nullable.
- Do NOT wrap event dispatch in try-catch — silent swallowing causes half-persisted entities.

## Testing

- JwtAuthTrait: `getJwtToken()` replaces static client. Get ALL tokens BEFORE creating test client.
- Test baseline: 1011 tests, 2995 assertions.
- `--filter DashboardApiTest` not `--filter Dashboard` (too broad).
- deptrac 2.0: no `baseline:` key — use `skip_violations:` inline. `--formatter=baseline` generates it.

## Fixture Data

| Type | References |
|------|-----------|
| Users | `admin`, `teammember`, `teamleader`, `assistent`, `nmbu` — all in dep-1 except nmbu (dep-3) |
| Departments | dep-1=NTNU/Trondheim, dep-2=UiB/Bergen, dep-3=NMBU/As, dep-4=UiO/Oslo |
| Semesters | semester-current, semester-previous (dynamic); semester-1 (Var 2013), semester-2 (Var 2015), semester-3 (Host 2015) |
| Roles | role-1=ROLE_USER, role-2=ROLE_TEAM_MEMBER, role-3=ROLE_TEAM_LEADER, role-4=ROLE_ADMIN |
| FieldOfStudy | fos-1/2=dep-1, fos-3=dep-2, fos-4=dep-3, fos-5=dep-4 |
| InterviewSchemas | ischema-1 (used), ischema-2 (unused, safe to delete) |
