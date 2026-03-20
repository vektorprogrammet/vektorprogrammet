# Software Developer Agent Memory

## Config Architecture

- `.env` committed to repo (Symfony standard). `.env.local` gitignored for secrets.
- JWT passphrase in `.env` is `dev_jwt_passphrase` — matches the PEM keys in `config/jwt/`.
- Static params (locale, session.name, remember_me.name, file paths) stay as YAML parameters in services.yaml
- Environment-varying values use `%env()%` syntax
- `services_prod.yaml` and `services_staging.yaml` override `google_api.disabled` flag
- `twig.yaml` holds `googleAnalyticsId: ~` (moved from parameters.yml)
- Dev SQLite path handled in `config/packages/dev/doctrine.yaml`, NOT via env var (DotEnv doesn't resolve `%kernel.project_dir%`)

## Docker / Composer

- Dockerfile uses `php:8.4-cli` (8.5 image not yet published), Bun for frontend build
- `composer install --no-dev` in Docker — `doctrine-fixtures-bundle` is in `require` (not `require-dev`), so fixtures work without dev deps
- `google/apiclient-services` trimmed from 130MB → 2MB via `pre-autoload-dump` cleanup script in `composer.json`. Only Drive, Directory, Gmail kept (listed in `extra`).
- After editing `extra.google/apiclient-services` list, must `rm -rf vendor/google/apiclient-services && composer install` to re-trigger cleanup
- `COMPOSER_PROCESS_TIMEOUT=600` needed for large packages in Docker (VirtioFS I/O is slow on macOS)
- `.dockerignore` excludes vendor/, node_modules/, var/ (11GB), .git/, tests/, docs/

## API Platform Patterns

### DTOs and operations
- `PUT` endpoints with no request body: keep DTO class empty, send `json_encode([])` in tests. Do NOT use `input: false`.
- POST endpoints: `output: false`, status code in attribute
- PUT endpoints: `read: false, output: false` (both needed for PUT on non-entity DTOs)
- PUT + DELETE on same URI template on same class = 405. Always use separate resource classes.
- POST + PUT on same class with different URI templates works fine.
- DELETE on standalone resource class MUST have a provider — `read: false` + `deserialize: false` alone causes "Could not resolve argument $data"
- POST+{id} pattern: do NOT use `read: false` — it prevents the provider from being called. Use `deserialize: false` only.
- Processors use `assert($data instanceof ...)` pattern
- Entity lookups via `$this->em->getRepository(...)->find(...)` with NotFoundHttpException
- JSON serialization omits null fields — don't `assertArrayHasKey` on nullable fields

### Provider behavior
- Provider runs BEFORE security in API Platform — 404 takes priority over 403
- For "forbidden for assistant" tests on provider-backed endpoints, create a real entity first
- Provider pattern for dashboard-style endpoints: inject repositories + services directly, use `SemesterRepository::findOrCreateCurrentSemester()`

### Events and side effects
- Do NOT wrap event dispatch in try-catch — silent exception swallowing causes half-persisted entities
- ReceiptSubscriber crashes in stateless API context (no session for flash messages)
- Let exceptions propagate; fix subscribers to handle API context

### Security
- Verify access_control regex patterns don't over-match (test with `^` anchor)
- Always cross-reference `config/packages/security.yaml` access_control before choosing API Platform `security:` attribute
- Processor-level auth: UnauthorizedHttpException (401) when no credentials, AccessDeniedHttpException (403) when insufficient role
- Mixed-auth endpoints: `PUBLIC_ACCESS` in access_control + `security: "true"` on operation, processor checks `$this->security->getUser()`

### Cache
- After creating/modifying API Platform resource classes, clear route cache: `php bin/console cache:clear --env=test`

## DDD Namespace Migration Patterns

- macOS `sed` does not support `\b` word boundaries — use `perl -pi` for namespace replacements
- When moving entities to a new namespace, check for **unqualified class references** that relied on same-namespace resolution (e.g., `User::class` in Interview entities that was resolved via `App\Entity` namespace)
- After moving entities, update `targetEntity:` references in **other** entities that point to the moved class (e.g., `User#interviews` → needs `use App\Interview\Infrastructure\Entity\Interview`)
- Non-Doctrine classes in `Infrastructure/Entity/` (like InterviewDistribution) must be excluded from service autodiscovery — they have constructor args that aren't services
- When moving subscribers out of `App\EventSubscriber\`, check if unused bindings remain (SmsSenderInterface, $env) — Symfony will error on unused bindings
- DQL strings contain inline FQCNs (e.g., `FROM App\Entity\Interview i`) — must be updated alongside `use` statements

## Entity Gotchas

### User
- `User::setPassword()` internally calls `password_hash(PASSWORD_BCRYPT, cost=12)` — do NOT use UserPasswordHasherInterface (double-hash)
- Username field is `user_name` (with underscore), not `userName`
- NOT NULL fields when creating: `gender` (boolean), `phone` (string), `picture_path` (string)
- Constructor sets: `picture_path='images/defaultProfile.png'`, `isActive=true`, `reservedFromPopUp=false`, `lastPopUpTime=new \DateTime('2000-01-01')`
- `Security::getUser()` returns `UserInterface|null` — always guard with `if (!$user instanceof \App\Entity\User)`

### Collections
- ManyToMany replacement on edit: `$entity->getCollection()->clear()` before adding new items
- `Team::getTeamMemberships()` lazy-loaded — after creating TeamMembership, query explicitly with repository
- AdmissionPeriod delete must cascade InfoMeeting (remove InfoMeeting BEFORE AdmissionPeriod — FK constraint)

### Other
- InterviewSubscriber expects `'datetime' => \DateTime` in event data, not string
- ExecutiveBoard is a singleton — `findBoard()` returns the single entity
- ConflictHttpException for FK constraint checks (409 Conflict)
- Survey::copy() clones survey + questions + alternatives, prepends "Kopi av ", resets id

## Testing Patterns

### JwtAuthTrait gotcha
- `getJwtToken()` calls `static::createClient()`, replacing the current static client
- Get ALL tokens BEFORE creating the test client
- Pattern: `$token1 = $this->getJwtToken('a'); $token2 = $this->getJwtToken('b'); $client = static::createClient();`

### Test conventions
- JwtAuthTrait for JWT auth in API tests
- Pattern: RequiresAuth (401), ForbiddenForAssistant (403), happy path, NotFound (404), validation edge cases
- DB is in-memory SQLite with DAMA DoctrineTestBundle — transaction rollback per test
- `--filter DashboardApiTest` not `--filter Dashboard` (too broad)
- `composer test:parallel` uses 256M — OOMs at ~1020+ tests. Use `php -d memory_limit=512M` directly.
- SQLite column naming: entity `targetAudience` → column `target_audience` (snake_case in raw SQL)
- Test baseline: 1011 tests, 2995 assertions

### Controller deprecation status
- 11 controllers fully covered by API: Department, UserAdmin, Semester, ChangeLog, SocialEvent, InterviewSchema, FieldOfStudy, Position, ExecutiveBoard, PasswordReset, Contact
- Controller tests removed for 7 of these (others had no controller tests)
- Controllers + Twig templates kept until React admin UI exists
- FrontEndController deleted (dead code — referenced nonexistent `client/build/`)
- Missing API test coverage: PasswordResetApiTest (reuse link, inactive user), ContactMessageApi (no test file)

### Recurring code review findings
- Status-code-only tests: assert response body/side effects, not just 200/204
- Silent exception swallowing: builders tend to add try/catch "for safety" — review that business logic exceptions propagate
- Unreachable error handling: FileUploader methods return string or throw, never falsy
- Eligibility logic: tests commonly skip condition branches, only test happy path
- `markTestSkipped()` for committed fixture data should be `assertNotNull()`
- `mixed` type hints on private methods when concrete Entity types are known

## Fixture Data

### Users
- `admin`, `teammember`, `teamleader`, `assistent` (Norwegian spelling), `nmbu`
- admin/teamleader/teammember/assistent all in dep-1 (fos-1). nmbu in dep-3 (fos-4).

### Departments
- dep-1=NTNU/Trondheim, dep-2=UiB/Bergen, dep-3=NMBU/As, dep-4=UiO/Oslo

### Semesters
- semester-current (dynamic), semester-previous (dynamic), semester-1 (Var 2013), semester-2 (Var 2015), semester-3 (Host 2015)

### Other
- AdmissionPeriods: dep-1+semester-current, dep-4+semester-current, dep-1+semester-previous, dep-1+semester-1, dep-2+semester-2, dep-3+semester-3, dep-4+semester-3, dep-1+semester-3
- InfoMeeting: only one, linked to `uio-admission-period-current` (dep-4+semester-current)
- Surveys: `team-survey-1` through `team-survey-4`, `school-survey-1`, `anonymous-school-survey-1`
- InterviewSchemas: `ischema-1` (used by interviews), `ischema-2` (unused, safe to delete)
- Roles: role-1=ROLE_USER, role-2=ROLE_TEAM_MEMBER, role-3=ROLE_TEAM_LEADER, role-4=ROLE_ADMIN
- No UserGroupCollection/UserGroup or SurveyNotificationCollection fixtures — tests create inline
- FieldOfStudy: fos-1/fos-2 = dep-1, fos-3 = dep-2, fos-4 = dep-3, fos-5 = dep-4
