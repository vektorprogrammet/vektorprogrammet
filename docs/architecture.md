# Architecture

## Controllers

~61 controllers, all using constructor DI. `BaseController` extends Symfony's `AbstractController` with shared helpers (`getDepartment()`, `getCurrentSemester()`). No service locator or `getDoctrine()` calls — all dependencies injected via constructors.

Routes are defined as `#[Route]` PHP 8 attributes directly on controller methods. Only 3 routes remain in `config/routes.yaml`: elfinder (3rd-party), liip_imagine (bundle), and logout (firewall-handled).

## Security / Roles

Role hierarchy (each level inherits from the previous):

```
ROLE_USER < ROLE_TEAM_MEMBER < ROLE_TEAM_LEADER < ROLE_ADMIN
```

- `User` implements `UserInterface` + `PasswordAuthenticatedUserInterface`
- `User::getRoles()` returns `string[]` (not Role entities)
- Templates use `user.roleEntities` for entity access (not `user.roles`)
- `Role::__toString()` returns `getRole()` (e.g. `ROLE_ADMIN`), not `getName()`
- `ReversedRoleHierarchy` at `App\Role\ReversedRoleHierarchy` — uses `getReachableRoleNames()` (Sf6 API)

## Service Patterns

- Services autodiscovered via `config/services.yaml` glob for: Controller, EventSubscriber, Form, Google, Mailer, Role, Security, Service, Sms, Twig, Validator
- `AccessControlService` uses lazy cache loading (`ensureCacheLoaded()` on first access, not in constructor)
- Session: use `RequestStack->getSession()`, not `SessionInterface` injection (not autowirable in Sf6)
- Password hashing: `security.password_hasher` (not `password_encoder`)

## Mailer

- Symfony Mailer (SwiftMailer removed in 2024)
- Production: Gmail transport sets `from` header automatically
- Dev/test: `Mailer::send()` must set explicit `from` header

## API Platform

JSON API at `/api/*` for the v2 React homepage.

**Configuration**: `config/packages/api_platform.yaml` maps entities (`src/App/Entity/`) and DTOs (`src/App/ApiResource/`).

**Serialization pattern**:
- Collection views: `normalizationContext: ['groups' => ['entity:read']]`
- Detail views: `entity:read` + `entity:detail` groups (adds relations)
- Cross-entity embedding via shared groups (e.g. `department:detail` on Team.$id)
- Back-references omit groups to prevent circular serialization

**Custom resources** (DTOs in `src/App/ApiResource/`, processors/providers in `src/App/State/`):
- `Statistics` + `StatisticsProvider` — aggregates user/assistant counts
- `ApplicationInput` + `ApplicationProcessor` — creates User + Application, dispatches event
- `ContactMessageInput` + `ContactMessageProcessor` — sends email via `App\Mailer\MailerInterface`
- `AdmissionSubscriberInput` + `AdmissionSubscriberProcessor` — idempotent subscriber on email+department
- `ProfileResource` + `ProfileProvider` + `ProfileProcessor` — authenticated user profile (GET/PUT `/api/me`)
- `PasswordResetRequest` + `PasswordResetRequestProcessor` — request password reset (POST `/api/password_resets`)
- `PasswordResetExecute` + `PasswordResetExecuteProcessor` — execute password reset (POST `/api/password_resets/{code}`)
- `TeamApplicationInput` + `TeamApplicationProcessor` — team application submission (POST `/api/team_applications`)
- `ExistingUserApplicationInput` + `ExistingUserApplicationProcessor` — existing user re-admission (POST `/api/applications/existing`)
- `PublicUserProfileProvider` — public user profiles (GET `/api/users/{id}`)
- `ArticleProcessor` — article CRUD (in progress)

**Auth**: JWT via `LexikJWTAuthenticationBundle`. Homepage endpoints use PUBLIC_ACCESS. Auth-required endpoints use `security: "is_granted('ROLE_USER')"`.

**Rate Limiting** (`symfony/rate-limiter`):
- Login: `login_throttling` on `secured_area` + `api_login` firewalls (5 attempts / 15 min)
- Password reset: 3 requests / hour (both Twig form + API endpoint)
- Contact form: 5 requests / hour
- Applications: 5 requests / hour
- Config: `config/packages/framework.yaml` under `framework.rate_limiter`. Test env overrides limits to 1000 in `config/packages/test/framework.yaml`.

## Migration History (2024-2026)

- `getDoctrine()` / `$this->get()` → constructor DI (all controllers)
- Doctrine annotations → `#[ORM\...]` PHP 8 attributes (all entities)
- `@Route` annotations → `#[Route]` attributes (all controllers)
- Removed `sensio/framework-extra-bundle` + `doctrine/annotations`
- YAML routes → controller attributes (~193 routes migrated)
- PHP 8.4 implicit nullable params fixed across 23 files
- Removed FOS REST bundle — all 7 endpoints (`/api/party/*`, `/api/account/*`) were dead code with zero consumers
- PHP 8.5 migration via Rector: `readonly` promoted properties, unused import cleanup, `catch` without variable, `array()` → `[]`, alphabetical imports (400 files, -1700 lines)
- PHP-CS-Fixer: `@Symfony` ruleset enforced across all 464 files (was 373 pre-existing violations)
- Rector configured for PHP 8.5 target (`withPhpSets(php85: true)`)
- Replaced hand-rolled `SlugMaker` with Symfony `SluggerInterface` (ICU transliteration for Norwegian chars)
- Added rate limiting via `symfony/rate-limiter` on login, password reset, contact form, application endpoints
- Config migration: old `config_{env}.yml` import chains → modern `config/packages/` structure with per-env overrides
- Test DB: file-based SQLite with backup/restore → in-memory SQLite with `dama/doctrine-test-bundle` (transaction rollback per test)
