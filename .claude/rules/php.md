---
paths:
  - "src/**/*.php"
  - "config/**/*.yml"
  - "config/**/*.yaml"
---

# PHP 8.5 / Symfony 6.4 / DDD Patterns

## DDD Namespace Conventions

Code is organized by bounded context, not by technical layer. Every class lives
under `App\{Context}\{Layer}\`:

```
App\Admission\Infrastructure\Entity\Application
App\Admission\Api\State\ApplicationProcessor
App\Admission\Domain\Rules\ApplicationStatusRule
App\Shared\Entity\Semester
App\Support\Infrastructure\Mailer\Mailer
```

**Domain layer** (`{Context}\Domain\`): Pure PHP only. No `use Doctrine\*`,
no `use Symfony\*`, no `use ApiPlatform\*`. If you need a framework import,
the class belongs in Infrastructure or Api, not Domain.

**Infrastructure layer** (`{Context}\Infrastructure\`): Framework-coupled.
May import from other contexts' Infrastructure (Doctrine requires this for
cross-context entity relations).

**Cross-context entity references**: Always use `::class` syntax in Doctrine
attributes, never short string names:
```php
// Correct:
#[ORM\ManyToOne(targetEntity: User::class)]

// Wrong — breaks when entities are in different namespaces:
#[ORM\ManyToOne(targetEntity: 'User')]
```

## Dependency Injection

Constructor DI only. No `getDoctrine()`, `$this->get()`, or
`$this->container->get()`. Without this: Sf6 runtime errors.

Session: `$this->requestStack->getSession()`. Not `SessionInterface` injection
— not autowirable in Sf6.

Password hashing: inject `UserPasswordHasherInterface`. Not `password_encoder`.

## PHP 8 Attributes

All mapping uses attributes, not annotations:
- Routing: `#[Route('/path', name: 'route_name')]`
- Doctrine: `#[ORM\Entity]`, `#[ORM\Column]`, etc.
- Validation: `#[Assert\NotBlank]`, `#[Assert\Length]`, etc.

## API Platform

DTOs in `{Context}/Api/Resource/`. Providers/processors in `{Context}/Api/State/`.

Serialization groups: `entity:read` on scalar props for collections. Add
`entity:detail` for detail views. Omit groups on relation properties entirely.
Without this: circular reference errors.

`#[ApiFilter]` is forbidden — `api-platform/doctrine-orm` isn't registered in
the test container. Use custom providers for filtering.

Auth-required operations: `security: "is_granted('ROLE_USER')"` on the operation.

Slugs: use `SluggerInterface` from `symfony/string` — handles Norwegian chars
(æ/ø/å) via ICU transliteration.

## Rate Limiting

Login: `login_throttling` in `security.yml` (firewall-level, automatic).

Other endpoints: inject `RateLimiterFactory` with naming convention
`$limiterNameLimiter` where `limiter_name` matches the key in
`framework.rate_limiter` config.

State processors: inject `RequestStack` for
`$this->requestStack->getCurrentRequest()?->getClientIp()`.

Test environment: `config_test.yml` overrides limits to 1000.

## PHP 8.5 Patterns

Constructor properties: `private readonly` promoted for all injected
dependencies. Only omit `readonly` when the property is reassigned after
construction (cache arrays, derived state).

Unused catch variables: `catch (\Exception)` not `catch (\Exception $e)`.

Imports: fully qualified in `use` statements, alphabetically ordered.
