---
paths:
  - "src/**/*.php"
  - "config/**/*.yml"
  - "config/**/*.yaml"
---

# PHP 8.5 / Symfony 6.4 Patterns

## Dependency Injection

Constructor DI only. No `getDoctrine()`, `$this->get()`, or `$this->container->get()`. Without this: Sf6 runtime errors (`getDoctrine()` removed).

Session: `$this->requestStack->getSession()`. Not `SessionInterface` injection — not autowirable in Sf6.

Password hashing: inject `UserPasswordHasherInterface`. Not `password_encoder` — removed in Sf6.

## PHP 8 Attributes

All mapping uses attributes, not annotations:
- Routing: `#[Route('/path', name: 'route_name')]`
- Doctrine: `#[ORM\Entity]`, `#[ORM\Column]`, etc.
- Validation: `#[Assert\NotBlank]`, `#[Assert\Length]`, etc.

No annotation imports (`use Doctrine\ORM\Mapping as ORM` with `/** @ORM\... */`). Without this: inconsistent with entire codebase, which was migrated to attributes in Sprint 8.

## API Platform

DTOs in `src/App/ApiResource/`. Providers/processors in `src/App/State/`. Without this: API Platform won't discover the resource.

Serialization groups: `entity:read` on scalar props for collections. Add `entity:detail` for detail views. Omit groups on relation properties entirely. Without this: circular reference errors during serialization.

`#[ApiFilter(SearchFilter::class)]` and similar built-in filters are forbidden — `api-platform/doctrine-orm` isn't registered in the test container. Without this: container compilation failure. Use custom providers for filtering instead.

Auth-required operations: `security: "is_granted('ROLE_USER')"` on the operation. Without this: endpoint is publicly accessible. The `api` firewall (`^/api`, `jwt: ~`) handles JWT validation.

Slugs: use `SluggerInterface` from `symfony/string` — handles Norwegian chars (æ/ø/å) via ICU transliteration. Not hand-rolled regex. Without this: Norwegian characters handled inconsistently or dropped.

## Rate Limiting

Login: `login_throttling` in `security.yml` (firewall-level, automatic). Not manual `RateLimiterFactory` in controllers. Without this: the security system intercepts POST before the controller method executes.

Other endpoints: inject `RateLimiterFactory` with naming convention `$limiterNameLimiter` where `limiter_name` matches the key in `framework.rate_limiter` config. Without this: Symfony can't autowire the correct limiter.

State processors: inject `RequestStack` for `$this->requestStack->getCurrentRequest()?->getClientIp()`. Without this: no access to client IP (processors don't receive `Request` directly).

Test environment: `config_test.yml` overrides limits to 1000. Without this: tests that submit forms repeatedly get 429 errors.

## PHP 8.5 Patterns

Constructor properties: `private readonly` promoted for all injected dependencies. Without this: mutable state where immutability is free. Only omit `readonly` when the property is reassigned after construction (cache arrays, derived state). Without this: `Cannot modify readonly property` at runtime.

Unused catch variables: `catch (\Exception)` not `catch (\Exception $e)` when `$e` is unused.

Imports: fully qualified in `use` statements, alphabetically ordered. `\Exception` not `Exception` in catch blocks.
