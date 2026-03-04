---
paths:
  - "src/**/*.php"
  - "config/**/*.yml"
  - "config/**/*.yaml"
---

# PHP / Symfony Patterns

## Dependency Injection

Constructor DI only. No `getDoctrine()`, `$this->get()`, or `$this->container->get()`. Without this: Sf6 deprecation warnings and runtime errors (`getDoctrine()` removed).

Session: `$this->requestStack->getSession()`. Not `SessionInterface` injection — not autowirable in Sf6.

Password hashing: inject `UserPasswordHasherInterface`. Not `password_encoder` — removed in Sf6.

## PHP 8 Attributes

All mapping uses attributes, not annotations:
- Routing: `#[Route('/path', name: 'route_name')]`
- Doctrine: `#[ORM\Entity]`, `#[ORM\Column]`, etc.
- Validation: `#[Assert\NotBlank]`, `#[Assert\Length]`, etc.

No annotation imports (`use Doctrine\ORM\Mapping as ORM` with `/** @ORM\... */`). Without this: inconsistent with entire codebase, which was migrated to attributes in Sprint 8.

## API Platform

DTOs in `src/App/ApiResource/`. Providers/processors in `src/App/State/`. Both registered in `config/api_platform.yml` mapping + `config/services.yml` autodiscovery.

Serialization groups: `entity:read` on scalar props for collections. Add `entity:detail` for detail views. Omit groups on relation properties entirely. Without this: circular reference errors during serialization.

`#[ApiFilter(SearchFilter::class)]` and similar built-in filters are forbidden — the filter classes live in `api-platform/doctrine-orm`, which isn't registered in the test container. Without this: container compilation failure. Use custom providers for filtering instead.

Auth-required operations: `security: "is_granted('ROLE_USER')"`. The `api` firewall (`^/api`, `jwt: ~`) handles JWT validation.

## FOS REST Coexistence

Never widen `format_listener` scope beyond `^/api/party`. The `stop: true` rule for `^/api/` and `zone` config restrict FOS REST to legacy routes. Without this: FOS REST intercepts API Platform responses, causing serialization errors.

`api_party` firewall must remain before `api` firewall in `security.yaml`. Without this: legacy party endpoints require JWT instead of session cookies.
