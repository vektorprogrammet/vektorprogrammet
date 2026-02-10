# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-10
**Phase**: Sprint 8 (DI Migration + Annotations → Attributes) — IN PROGRESS
**Branch**: `modernize/sprint-1-remove-dead-dependencies`
**Plan**: `.planning/phases/sprint-8/PLAN.md`

## Progress

- [x] Sprint 1: Remove dead dependencies
- [x] Sprint 2: Package upgrades
- [x] Sprint 3: AppBundle → App namespace
- [x] Sprint 4: Directory restructure
- [x] Sprint 5: Symfony 3.4 → 4.4
- [x] Sprint 6: Symfony 4.4 → 5.4 (committed: `5d4f2a15`)
- [x] Sprint 7: Symfony 5.4 → 6.4 — COMPLETE (3 commits)
- [x] Sprint 7b: Fix 15 Sf6 test regressions — COMPLETE
- [x] Agent tooling: composer scripts, php-cs-fixer, PHPStan, CI workflow
- [ ] **Sprint 8**: DI migration + annotations → attributes (7 phases)
  - [x] Phase 1: Remove sensio/framework-extra-bundle (`fb1276ae`)
  - [x] Phase 2: Convert 35 repos → ServiceEntityRepository + services.yml (`6112447e`)
  - [x] Phase 3: Entity annotations → attributes + custom validators (`be6db9eb`)
  - [ ] **Phase 4**: Controller DI migration (60 controllers, 5 batches) — NOT STARTED
  - [ ] Phase 5: Remove BaseController bridge methods
  - [ ] Phase 6: @Route annotations → #[Route] attributes (21 controllers)
  - [ ] Phase 7: Config updates + remove doctrine/annotations
- [ ] Sprint 9: YAML route consolidation, frontend, PHPStan

## Sprint 8 Session Summary (2026-02-10)

### Completed Phases

**Phase 1** — Remove sensio/framework-extra-bundle
- Removed from composer.json, config/bundles.php, config/config.yml
- Used `composer remove` to keep other package versions stable
- Reduced memory limits from 512M to 256M (actual peak: 158MB)

**Phase 2** — Convert repos to ServiceEntityRepository
- Converted 34 repos from EntityRepository to ServiceEntityRepository
- Moved TeamInterestRepository from Entity/ to Entity/Repository/ (fixed namespace)
- Updated TeamInterest entity repositoryClass to FQCN
- Deleted 2 orphan repos: AdmissionRepository, OpptakRepository
- Added Entity/Repository autodiscovery to services.yml
- Fixed SocialEventRepository which had leading backslash in imports

**Phase 3** — Entity annotations → attributes + custom validators
- Converted 49 entities from @ORM\/@Assert\/@CustomAssert\ to PHP 8 attributes
- Converted 5 custom validators from @Annotation to #[Attribute]
- Switched Doctrine mapping type: annotation → attribute (config.yml)
- Fixed multiple conversion issues:
  - 13 entities missing #[ORM\Entity] (bare @ORM\Entity was dropped)
  - JoinTable nested annotations → separate attributes (JoinColumn, InverseJoinColumn)
  - options arrays: `"default"=false` → `"default" => false`
  - Extra closing parens from `{`→`[` conversion
  - Malformed docblocks (`**/` then `*/`)
  - SurveyTaken groups: string → array
  - Pre-existing SurveyNotification inversedBy mapping bug fixed

### Known Issues

- `testShouldSendInfoMeetingNotification` is flaky near midnight (time-of-day dependent)
- First test run after cache clear shows failures (Twig warmup); second run is clean
- `composer update` without targeting can bump Sf packages to v7 (breaks UserRepository)

## Next Session

Start Phase 4: Controller DI migration. This is the largest phase (60 controllers).

Key approach:
1. Add optional constructor to BaseController (DepartmentRepo, SemesterRepo, ManagerRegistry)
2. Migrate controllers in 5 batches (14-18 controllers each)
3. Replace `getDoctrine()->getRepository()` with injected repos
4. Replace `$this->get('service_id')` with injected services
5. Special case: Api\PartyController extends AbstractFOSRestController

Important: Clear var/cache/tes_/ before running tests after changes. The stale Twig cache causes false failures on first run.

## Test Results (496 tests)

**0 errors + 0 failures** — All 496 tests pass (1150 assertions). Baseline tracked in `.planning/test-baseline.md`.

## Reference

- Sprint 8 plan: `.planning/phases/sprint-8/PLAN.md`
- Test commands & workflow: `docs/testing.md`
- Architecture details: `docs/architecture.md`
