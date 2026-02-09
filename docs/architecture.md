# Architecture

## Controllers

`BaseController` extends Symfony's `AbstractController` with:
- `getSubscribedServices()` providing ~35 services via service locator
- Bridge methods `getDoctrine()` and `get()` — deferred to Sprint 8 for proper DI migration
  - 257 `getDoctrine()` calls across 55 controllers
  - 149 `$this->get()` calls across 40 controllers

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

- Symfony Mailer (not SwiftMailer — migrated in Sprint 7)
- Production: Gmail transport sets `from` header automatically
- Dev/test: `Mailer::send()` must set explicit `from` header

## Deferred Migrations (Sprint 8)

- `getDoctrine()` → injected repositories (257 calls, 55 controllers)
- `$this->get()` → constructor DI (149 calls, 40 controllers)
- Annotations → PHP 8 attributes (663 annotations)
- Remove `sensio/framework-extra-bundle` + `doctrine/annotations`
