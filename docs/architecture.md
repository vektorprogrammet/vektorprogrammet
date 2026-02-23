# Architecture

## Controllers

~61 controllers, all using constructor DI. `BaseController` extends Symfony's `AbstractController` with shared helpers (`getDepartment()`, `getCurrentSemester()`). No service locator or `getDoctrine()` calls — all dependencies injected via constructors.

Routes are defined as `#[Route]` PHP 8 attributes directly on controller methods. Only 3 routes remain in `config/routing.yml`: elfinder (3rd-party), liip_imagine (bundle), and logout (firewall-handled).

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

- Services autodiscovered via `config/services.yml` glob for: Controller, EventSubscriber, Form, Google, Mailer, Role, Security, Service, Sms, Twig, Validator
- `AccessControlService` uses lazy cache loading (`ensureCacheLoaded()` on first access, not in constructor)
- Session: use `RequestStack->getSession()`, not `SessionInterface` injection (not autowirable in Sf6)
- Password hashing: `security.password_hasher` (not `password_encoder`)

## Mailer

- Symfony Mailer (SwiftMailer removed in 2024)
- Production: Gmail transport sets `from` header automatically
- Dev/test: `Mailer::send()` must set explicit `from` header

## API Platform

JSON API at `/api/*` for the v2 React homepage. Coexists with legacy FOS REST endpoints at `/api/party/*`.

**Configuration**: `config/api_platform.yml` maps entities (`src/App/Entity/`) and DTOs (`src/App/ApiResource/`).

**Serialization pattern**:
- Collection views: `normalizationContext: ['groups' => ['entity:read']]`
- Detail views: `entity:read` + `entity:detail` groups (adds relations)
- Cross-entity embedding via shared groups (e.g. `department:detail` on Team.$id)
- Back-references omit groups to prevent circular serialization

**Custom resources** (DTOs in `src/App/ApiResource/`, processors/providers in `src/App/State/`):
- `Statistics` + `StatisticsProvider` — aggregates counts from UserRepository + AssistantHistoryRepository
- `ApplicationInput` + `ApplicationProcessor` — creates User + Application, dispatches `ApplicationCreatedEvent`
- `ContactMessageInput` + `ContactMessageProcessor` — sends email via `App\Mailer\MailerInterface`
- `AdmissionSubscriberInput` + `AdmissionSubscriberProcessor` — creates AdmissionSubscriber entity (idempotent on email+department)
- `ProfileResource` + `ProfileProvider` + `ProfileProcessor` — authenticated user profile (GET/PUT `/api/me`), first auth-required endpoint

**Auth**: JWT via `LexikJWTAuthenticationBundle`. Homepage endpoints use PUBLIC_ACCESS. Auth-required endpoints use `security: "is_granted('ROLE_USER')"`. Legacy `/api/party/*` uses session auth via dedicated `api_party` firewall.

**FOS REST coexistence**: `format_listener` scoped to `^/api/party` (not `^/api`), `stop: true` rule for `^/api/`, `zone` config restricts FOS REST to legacy routes. Without this, FOS REST intercepts API Platform responses.

## Migration History (2024-2026)

- `getDoctrine()` / `$this->get()` → constructor DI (all controllers)
- Doctrine annotations → `#[ORM\...]` PHP 8 attributes (all entities)
- `@Route` annotations → `#[Route]` attributes (all controllers)
- Removed `sensio/framework-extra-bundle` + `doctrine/annotations`
- YAML routes → controller attributes (~193 routes migrated)
- PHP 8.4 implicit nullable params fixed across 23 files
- Rector configured for automated PHP deprecation fixes
