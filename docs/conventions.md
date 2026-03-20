# Conventions

Project conventions and patterns. Keep this updated as the codebase evolves.

> **For agents:** Invoke the `php-conventions` skill before writing or modifying PHP code. It contains the full reference for all patterns below.

## Guiding Principles

**Functional Core, Imperative Shell (FCIS):** Pure business rules in `Domain/`,
framework-coupled code in `Infrastructure/` and `Api/`. The domain layer has zero
Symfony/Doctrine dependencies. Doctrine entities stay as Infrastructure — the
domain layer holds extracted logic (rules, value objects, computations), not
entity mirrors.

**Domain-Driven Design (DDD):** Code organized by bounded context
(Admission, Interview, Organization, etc.), not by technical layer. Each context
owns its entities, services, API resources, and domain rules. Cross-context
imports only allowed at the Infrastructure level — Domain layers never import
across context boundaries.

**Spec:** `docs/superpowers/specs/2026-03-20-ddd-fcis-analysis-design.md`

## Bounded Context Conventions

- New code goes in the appropriate bounded context directory under `src/App/`.
- Domain rules are pure PHP classes — testable without database or framework.
- Doctrine `targetEntity` always uses `::class` syntax, never short strings.
- Cross-context entity references: Department (Organization), User (Identity),
  Semester (Shared). Reference by ID at the domain level; Infrastructure may
  import the entity directly.

## PHP 8.5

- `private readonly` promoted constructor properties for all injected dependencies
- Omit `readonly` only when the property is reassigned after construction
- `catch (\Exception)` — drop variable when unused
- `[]` not `array()`, `\Exception` not `Exception` in catch/use
- Rector (`rector.php`) configured for PHP 8.5 target

## Controllers

- All extend `BaseController` (which extends `AbstractController`)
- Constructor DI for all dependencies — no service locator
- `#[Route]` PHP 8 attributes for routing
- Only 3 routes in `config/routes.yaml`: elfinder, liip_imagine, logout
- Legacy Twig controllers are deprecated — new endpoints use API Platform

## Entities

- `#[ORM\...]` PHP 8 attributes for Doctrine mapping (no annotations)
- `#[Assert\...]` for validation constraints
- Custom validators use `#[\Attribute]`
- `targetEntity` must use `::class` syntax (not short strings)

## Services

- Autodiscovered via `config/services.yaml` per bounded context
- `private readonly` promoted constructor properties for all DI
- Session: `RequestStack->getSession()`, not `SessionInterface`
- Password: `UserPasswordHasherInterface`, not `password_encoder`
- Mailer: Symfony Mailer — dev/test must set explicit `from` header

## Testing

**Always clear cache before running tests** after moving files, changing
namespaces, or modifying Symfony config:
```bash
php bin/console cache:clear && php -d memory_limit=512M bin/phpunit
```

- `composer test` for full suite sequential (~280s, sets 256M memory limit)
- `composer test:parallel` for parallel via ParaTest -p4 (~67s)
- `bin/phpunit --filter=TestName` for targeted runs
- In-memory SQLite with `dama/doctrine-test-bundle` — transaction rollback per
  test, no file I/O
- `dangerouslyDisableSandbox: true` always — SQLite writes + vendor reads

## API Platform

- DTOs in `{Context}/Api/Resource/`, providers/processors in `{Context}/Api/State/`
- Serialization groups: `entity:read` for collections, + `entity:detail` for detail views
- Relations: omit `#[Groups]` to prevent circular references
- Auth: `security: "is_granted('ROLE_USER')"` on operations needing JWT
- Public endpoints: `security: 'PUBLIC_ACCESS'`
- No `#[ApiFilter]` — doctrine-orm bridge not registered. Use custom providers.
- Slugs: use `SluggerInterface` (Symfony String component)

## Rate Limiting

- Login: `login_throttling` in `config/packages/security.yaml` (5 attempts / 15 min)
- Other endpoints: inject `RateLimiterFactory` — naming: `$limiterNameLimiter`
- State processors: inject `RequestStack` for `getClientIp()`
- Test overrides in `config/packages/test/framework.yaml` (limit: 1000)

## Twig 3

- No `for...if` — use `|filter()`
- No blocks inside `if` — put conditional inside block
- `{% apply spaceless %}...{% endapply %}` not `{% spaceless %}`
