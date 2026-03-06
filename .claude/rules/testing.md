---
paths:
  - "tests/**/*.php"
  - "phpunit.xml.dist"
---

# Testing

## Running Tests

`composer test` or `php -d memory_limit=512M bin/phpunit` for full suite. `composer test` uses 256M which OOMs at ~640+ tests — bump to 512M if needed. Always `dangerouslyDisableSandbox: true` — SQLite writes + vendor reads require it.

Baseline in MEMORY.md. Never commit if new failures appear.

Suites: `composer test:unit` (fast, <1s), `composer test:controller` (~110s), `composer test:availability` (~67s), `composer test:parallel` (all, 4 workers, ~103s).

## Testing Philosophy

Every test must verify a **domain-specific acceptance criterion** — a behavior that matters to users or system correctness. Before writing a test, answer: "What domain rule or contract does this catch if broken?"

- **Test contracts, not implementations.** Assert what the system does (HTTP status, response shape, business rules enforced), not how it does it (which class was called, what type was returned).
- **Don't test the language or framework.** Type checks, `instanceof` assertions, "returns the right type" — these belong to the compiler/runtime, not the test suite. If PHP or Symfony guarantees it, don't re-verify it.
- **Test boundary enforcement.** Auth (401/403), input validation, cross-department access control, role restrictions — these are public-facing contracts that must be verified.
- **Prioritize practical paths.** Cover all reachable code paths. For unreachable defensive branches (type system too dynamic, data anomalies that can't occur through normal usage), add a comment explaining why instead of mocking unrealistic scenarios.
- **95% coverage target on new API code.** Accept lower for genuinely unreachable branches. Never pad coverage with meaningless assertions.

## Writing Tests

Integration tests: extend `BaseWebTestCase`. Use role-specific clients: `createAnonymousClient()`, `createAssistantClient()`, `createTeamMemberClient()`, `createTeamLeaderClient()`, `createAdminClient()`. Credentials: username = role name, password = `1234`.

Unit tests: extend `BaseKernelTestCase`.

API tests: extend `BaseWebTestCase`, `use JwtAuthTrait;`. Test DTOs/processors via HTTP using `getJwtToken('admin')` for auth. Not by instantiating processors directly. Without this: missing middleware, no request context, unrealistic coverage.

## Known Issues

`testShouldSendInfoMeetingNotification` flaky near midnight — time-dependent logic. Retry if fails at 23:5x/00:0x.
