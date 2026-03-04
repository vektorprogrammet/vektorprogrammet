# Troubleshooting

Error → fix lookup for common issues. Organized by category.

## Environment

**SQLite "disk I/O error"** or schema:create fails:
→ Service querying DB in constructor — make DB queries lazy (ensureCacheLoaded pattern)
→ Or: `rm -f var/data/test.db var/data/test.db.bk && rm -rf var/cache/test/`

**"no such table: access_rule"** in test output:
→ AccessRule feature deleted upstream but retained in this fork. Harmless bootstrap noise — lazy-loaded AccessControlService queries at boot before test DB created. Ignore.

**Stale Twig cache after entity/config changes → mass test failures**:
→ `rm -rf var/cache/tes_/` before running tests; first run compiles ~200 templates

**composer update without targeting → Sf7 bumps**:
→ Use `composer remove pkg` or `composer update specific-pkg` to avoid v7 drift
→ Always check `composer show "symfony/*"` after dependency changes — transitive deps can pull v7

**PHPStan shows 51 "errors" that are all unmatched ignore patterns**:
→ Stale ignores in phpstan.neon from already-fixed deprecations. Need cleanup pass.

## Symfony 6 API

**UsernamePasswordToken "too few arguments"**:
→ Sf6 removed credentials param from constructor

**"kernel.root_dir" not found**:
→ Use `kernel.project_dir` instead

**Mailer profiler "getMessages()" undefined**:
→ Sf6: `$collector->getEvents()->getMessages()` not `$collector->getMessages()`

**"getMasterRequest()" undefined**:
→ Sf6: use `getMainRequest()`

**SessionInterface not autowirable**:
→ Use `RequestStack->getSession()`

**"enable_authenticator_manager" error**:
→ Must be REMOVED entirely from security.yml in Sf6

**"storage_id" error in session config**:
→ Renamed to `storage_factory_id` in Sf6

**"ContainerAwareCommand" not found**:
→ Use `Command` with constructor DI; add to services.yml glob

**Mailer sends without from address**:
→ Sf Mailer requires explicit `from` header — Gmail sets it in prod, but test/dev needs it in Mailer::send()

**"password_encoder" not found**:
→ Renamed to `security.password_hasher` in Sf6

**"getReachableRoles()" not found**:
→ Sf6: `getReachableRoleNames()` (string[] in/out, not Role objects)

**"Cannot instantiate Role"**:
→ `Symfony\Component\Security\Core\Role\Role` has private constructor in Sf6

## Twig 3

**"for...if" error**:
→ `for...if` → `|filter()` syntax

**"spaceless" filter error**:
→ Use `{% apply spaceless %}...{% endapply %}`

**"block cannot be nested inside if"**:
→ Move conditional inside the block, not block inside conditional

**"asset() requires string, not null"**:
→ `asset()` needs non-null string — add null guards

**Form prototype double-render error**:
→ Use `setRendered()` on prototypes, not `form_rest()` — Sf6 strict mode

## PHPUnit / Testing

**Mock return type mismatch**:
→ PHPUnit enforces return types on mocks — use real objects (e.g. real `Request`)

**Session persists between logins in tests**:
→ Sf6 WebTestCase singleton client keeps session — clear cookie jar between login attempts

**"createClient() already called"**:
→ Sf6 doesn't allow multiple createClient() — use ensureKernelShutdown() or reuse client

**ParaTest: more failures with default runner than WrapperRunner**:
→ Default runner spawns separate PHP processes per test FILE — more double-boot errors (10 vs 1). Use `--runner=WrapperRunner` which preserves static state across files within a worker.

**ReceiptControllerTest::testDelete fails with redirect(null)**:
→ Pre-existing bug: `ReceiptController.php:267` passes null URL to `redirect()`. Surfaces with fresh client state.

**PHPUnit code coverage OOM on controller/availability tests**:
→ Coverage overhead on Doctrine/Symfony is ~50x: single controller test 5 MB → 512+ MB with coverage
→ Memory leak fixes (EM clear, static client reset) work for regular tests but don't affect coverage overhead
→ Root cause: Coverage tracking on every line of Doctrine metadata loading, entity hydration, Twig rendering
→ Solution: Run coverage per suite (`--testsuite=unit`) to isolate. Accept unit baseline (9%) until optimization feasible.

## API Platform + FOS REST

**API Platform returns FOS REST serialization errors / wrong format**:
→ FOS REST `format_listener` and `view_response_listener: 'force'` intercept API Platform responses
→ Fix: Scope FOS REST format_listener to `^/api/party` (not `^/api`), add `stop: true` rule for `^/api/`, and configure `zone` to restrict FOS REST to legacy routes only
→ See `config/config.yml` lines 263-277

**Existing `/api/party/*` endpoints return 401 after adding JWT firewall**:
→ The `^/api` JWT firewall catches party routes that use session auth
→ Fix: Add `api_party` firewall (session-based) BEFORE the `api` firewall in `config/security.yml`

**API Platform entity returns circular reference error**:
→ Doctrine relations (ManyToOne, ManyToMany) cause infinite serialization loops
→ Fix: Add `normalizationContext: ['groups' => ['entity:read']]` to `#[ApiResource]`, then `#[Groups(['entity:read'])]` only on scalar properties. Omit groups on relation properties.

## Annotation → Attribute Conversion

**Bare @ORM\Entity dropped → entities not recognized**:
→ Entities without repositoryClass had bare `@ORM\Entity` — must still emit `#[ORM\Entity]`

**Nested @ORM\JoinColumn inside JoinTable**:
→ Split into stacked attributes: `#[ORM\JoinTable(name:)]` + `#[ORM\JoinColumn(...)]` + `#[ORM\InverseJoinColumn(...)]`

**options array "default"=false**:
→ `{` → `[` is correct but `=` inside array must become `=>`

**groups must be arrays**:
→ `groups={"foo"}` → `groups: ["foo"]` not `groups: "foo"`

## Claude Code

**"classifyHandoffIfNeeded is not defined" on subagent completion**:
→ Known Claude Code bug (v2.1.x). Internal function missing from build. Agent work completes fine — only the cleanup/handoff step crashes, so task reports "failed" even though all edits were applied. Safe to ignore. Affects custom subagents only (not built-in Explore/Plan/Bash).
→ Refs: [#22312](https://github.com/anthropics/claude-code/issues/22312), [#22087](https://github.com/anthropics/claude-code/issues/22087)

**SubagentStop hook fields all empty/unknown**:
→ Hook event data arrives via **stdin** (use `DATA=$(cat)`), NOT `$CLAUDE_HOOK_EVENT_DATA`. The env var is empty.
→ Fields: `agent_type`, `agent_id`, `agent_transcript_path`, `stop_hook_active` (bool), `session_id`, `cwd`, `permission_mode`. No `agent_name` or `stop_reason`.

**Hook events silently ignored in settings.local.json**:
→ Hook events (`Stop`, `PostToolUse`, etc.) must be inside a `"hooks": {}` object, NOT as top-level keys. Top-level keys are silently ignored.

## Infrastructure / Config

**Dotenv "true" argument error**:
→ `new Dotenv(true)` → `new Dotenv()->usePutenv()`

**PSR-3 method signature mismatch**:
→ v3: methods need `\Stringable|string $message` + `: void`

**DataFixtures method signature error**:
→ v2: `load(): void`, `getOrder(): int`
