# Conventions

Project conventions and patterns. Keep this updated as the codebase evolves.

## PHP 8.5

- `private readonly` promoted constructor properties for all injected dependencies
- Omit `readonly` only when the property is reassigned after construction (cache arrays, derived state)
- `catch (\Exception)` — drop variable when unused
- `[]` not `array()`, `\Exception` not `Exception` in catch/use
- Rector (`rector.php`) configured for PHP 8.5 target — run `bin/rector process src/` after adding new files

## Controllers
- All extend `BaseController` (which extends `AbstractController`)
- Constructor DI for all dependencies — no service locator
- `#[Route]` PHP 8 attributes for routing
- Only 3 routes in `config/routes.yaml`: elfinder (3rd-party), liip_imagine (bundle), logout (firewall)

## Entities
- `#[ORM\...]` PHP 8 attributes for Doctrine mapping (no annotations)
- `#[Assert\...]` for validation constraints
- Custom validators use `#[\Attribute]`

## Services
- Autodiscovered via `config/services.yaml`
- `private readonly` promoted constructor properties for all DI
- Session: `RequestStack->getSession()`, not `SessionInterface` (not autowirable in Sf6)
- Password: `security.password_hasher`, not `password_encoder`
- Mailer: Symfony Mailer — dev/test must set explicit `from` header

## Testing
- `composer test` for full suite sequential (~175s, sets 256M memory limit)
- `composer test:parallel` for parallel via ParaTest -p4 (~67s)
- `bin/phpunit --filter=TestName` for targeted runs
- In-memory SQLite with `dama/doctrine-test-bundle` — transaction rollback per test, no file I/O
- Parallel: each ParaTest worker gets its own in-memory DB via DAMA static connections

## Tooling
- Rector (`rector.php`) for automated PHP version migration (configured for PHP 8.5)
- PHP-CS-Fixer for code style (`composer lint` / `composer fix`)
- PHPStan level 1 (`composer analyse`)

## API Platform
- DTOs in `src/App/ApiResource/`, providers/processors in `src/App/State/`
- Serialization groups: `entity:read` for collections, + `entity:detail` for detail views
- Relations: omit `#[Groups]` to prevent circular references
- Auth: `security: "is_granted('ROLE_USER')"` on operations needing JWT
- Public endpoints: `security: 'PUBLIC_ACCESS'`
- No `#[ApiFilter]` — doctrine-orm bridge not registered. Use custom providers.
- Slugs: use `SluggerInterface` (Symfony String component) — handles Norwegian chars via ICU transliteration

## Rate Limiting
- Login: `login_throttling` in `config/packages/security.yaml` (5 attempts / 15 min, both form + API login). Disabled in `config/packages/test/security.yaml`.
- Other endpoints: inject `RateLimiterFactory` — naming convention: `$limiterNameLimiter` where `limiter_name` is from `config/packages/framework.yaml`
- State processors: inject `RequestStack` for `getClientIp()` since they don't have `Request` params
- Config: `config/packages/framework.yaml` → `framework.rate_limiter`. Test overrides in `config/packages/test/framework.yaml` (limit: 1000)

## Agent Design
- User-level agents (`~/.claude/agents/`) for project-agnostic tools
- Project-level agents (`.claude/agents/`) for project-specific workflows
- Structural least-privilege: restrict tool list in frontmatter, don't rely on prompt instructions alone
- Structured output format (RESULT/Findings/Sources/Confidence) for parseable agent returns

## Twig 3
- No `for...if` — use `|filter()`
- No blocks inside `if` — put conditional inside block
- `{% apply spaceless %}...{% endapply %}` not `{% spaceless %}`
