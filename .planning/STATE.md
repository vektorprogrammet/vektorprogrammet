# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-10
**Phase**: Sprint 9 — IN PROGRESS (routes + deprecations done, cleanup remaining)
**Branch**: `modernize/sprint-1-remove-dead-dependencies` (9 commits ahead of origin, not pushed)

## Progress

- [x] Sprint 1: Remove dead dependencies
- [x] Sprint 2: Package upgrades
- [x] Sprint 3: AppBundle → App namespace
- [x] Sprint 4: Directory restructure
- [x] Sprint 5: Symfony 3.4 → 4.4
- [x] Sprint 6: Symfony 4.4 → 5.4 (`5d4f2a15`)
- [x] Sprint 7: Symfony 5.4 → 6.4 (3 commits)
- [x] Sprint 7b: Fix 15 Sf6 test regressions
- [x] Agent tooling: composer scripts, php-cs-fixer, PHPStan, CI workflow
- [x] **Sprint 8**: DI migration + annotations → attributes (7 phases)
- [ ] **Sprint 9**: Modernization & cleanup
  - [x] Remove dead routing_api.yml and file_uploader route (`27dd2bd7`)
  - [x] YAML routes → #[Route] controller attributes — 48 controllers (`f7add72a`)
  - [x] PHP 8.4 implicit nullable params — manual 11 files (`72d3ef38`)
  - [x] PHP 8.4 implicit nullable params — Rector sweep 12 files (`90a0f339`)
  - [x] usort bool → spaceship operator (`4a53f539`)
  - [x] Add Rector tooling (`7cb22d29`)
  - [ ] Remaining deprecations: strlen(null), ${var} string interpolation
  - [ ] PHPStan level increase
  - [ ] Frontend modernization

## Sprint 9 Session Summary

### YAML Route Consolidation
- Moved ~193 YAML route definitions to #[Route] attributes on 48 controllers
- routing.yml now only contains: elfinder (3rd-party), logout (firewall), liip_imagine
- 260 routes preserved (identical count before/after)

### PHP 8.4 Deprecation Fixes
- Installed Rector with ExplicitNullableParamTypeRector
- Fixed implicit nullable params across 23 files total (11 manual + 12 Rector)
- Fixed usort bool comparison in TeamAdminController (spaceship operator)
- Remaining vendor deprecations (knp-paginator, sentry) — need package updates

### Remaining Deprecations (from test output)
- `strlen(null)` in `AssetExtension.php:49` — pass empty string or add null check
- `${var}` string interpolation in `AssistantControllerTest.php:63-69` — use `{$var}`
- `SponsorsController::sponsorEditAction()` optional param before required — reorder
- Vendor: knp-paginator, sentry-symfony need upgrades for PHP 8.4

### Skill/Agent System Restructure (Phase 1)
- Created `/orchestrate` skill (session router, delegates to agents)
- Created `/plan` skill (interactive planner with templates)
- Created 4 agent definitions: coding, verify, state-sync, execute-plan
- Deprecated 8 old skills (user-invocable: false)
- Updated CLAUDE.md routing table and workflow
- Two rounds of agent-expert review — all issues resolved (5/5 orchestration, 5/5 Anthropic alignment)
- Commits: `c759edd6`, `857c3e39`

### Session Notes
- Last session (2026-02-10) cut short by terminal becoming unresponsive. All work was committed and valid.
- 9 commits not yet pushed to origin. Working tree clean.

## Known Issues

- `testShouldSendInfoMeetingNotification` is flaky near midnight (time-of-day dependent)
- First test run after cache clear shows failures (Twig warmup); second run is clean
- `composer update` without targeting can bump Sf packages to v7 (breaks UserRepository)
- Tests MUST use `composer test` (256M memory) — direct `php bin/phpunit` uses 128M default

## Test Results (496 tests)

**0 errors + 0 failures** — All 496 tests pass (1150 assertions).

## Reference

- Sprint 8 plan: `.planning/phases/sprint-8/PLAN.md`
- Test commands & workflow: `docs/testing.md`
- Architecture details: `docs/architecture.md`
