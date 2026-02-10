# Troubleshooting

Error → fix lookup for common issues. Organized by category.

## Environment

**SQLite "disk I/O error"** or schema:create fails:
→ Service querying DB in constructor — make DB queries lazy (ensureCacheLoaded pattern)
→ Or: `rm -f var/data/test.db var/data/test.db.bk && rm -rf var/cache/test/`

**"no such table: access_rule"** in test output:
→ Harmless bootstrap noise — kernel boots before test DB exists. Ignore.

**Stale Twig cache after entity/config changes → mass test failures**:
→ `rm -rf var/cache/tes_/` before running tests; first run compiles ~200 templates

**composer update without targeting → Sf7 bumps**:
→ Use `composer remove pkg` or `composer update specific-pkg` to avoid v7 drift

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
