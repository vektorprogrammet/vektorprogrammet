# Conventions

Project conventions and patterns. Keep this updated as the codebase evolves.

## Controllers
- All extend `BaseController` (which extends `AbstractController`)
- Constructor DI for all dependencies — no service locator
- `#[Route]` PHP 8 attributes for routing
- Only 3 routes in `config/routing.yml`: elfinder (3rd-party), liip_imagine (bundle), logout (firewall)

## Entities
- `#[ORM\...]` PHP 8 attributes for Doctrine mapping (no annotations)
- `#[Assert\...]` for validation constraints
- Custom validators use `#[\Attribute]`

## Services
- Autodiscovered via `config/services.yml`
- Session: `RequestStack->getSession()`, not `SessionInterface` (not autowirable in Sf6)
- Password: `security.password_hasher`, not `password_encoder`
- Mailer: Symfony Mailer — dev/test must set explicit `from` header

## Testing
- `composer test` for full suite sequential (~193s, sets 256M memory limit)
- `composer test:parallel` for parallel via ParaTest -p4 (~103s)
- `bin/phpunit --filter=TestName` for targeted runs
- SQLite test DB — bootstrap handles create/fixture/backup automatically
- Per-worker DB isolation via `TEST_TOKEN` env var for parallel runs

## Tooling
- Rector (`rector.php`) for automated PHP deprecation fixes
- PHP-CS-Fixer for code style (`composer lint` / `composer fix`)
- PHPStan level 1 (`composer analyse`)

## Agent Design
- User-level agents (`~/.claude/agents/`) for project-agnostic tools
- Project-level agents (`.claude/agents/`) for project-specific workflows
- Structural least-privilege: restrict tool list in frontmatter, don't rely on prompt instructions alone
- Structured output format (RESULT/Findings/Sources/Confidence) for parseable agent returns

## Twig 3
- No `for...if` — use `|filter()`
- No blocks inside `if` — put conditional inside block
- `{% apply spaceless %}...{% endapply %}` not `{% spaceless %}`
