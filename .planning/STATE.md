# State: Vektorprogrammet Monolith Symfony Upgrade

**Updated**: 2026-02-10
**Phase**: Sprint 8 — COMPLETE. Sprint 9 NEXT.
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
- [x] **Sprint 8**: DI migration + annotations → attributes (7 phases) — COMPLETE
  - [x] Phase 1: Remove sensio/framework-extra-bundle (`fb1276ae`)
  - [x] Phase 2: Convert 35 repos → ServiceEntityRepository + services.yml (`6112447e`)
  - [x] Phase 3: Entity annotations → attributes + custom validators (`be6db9eb`)
  - [x] Phase 4: Controller DI migration — 60 controllers in 4 batches (`1dd1501d`..`e4a5b7e4`)
  - [x] Phase 5: Remove BaseController bridge methods + service locator (`6b8671d7`)
  - [x] Phase 6: @Route annotations → #[Route] attributes — 21 controllers (`ee8b8612`)
  - [x] Phase 7: Config updates + remove doctrine/annotations (`465ee1a4`)
- [ ] Sprint 9: YAML route consolidation, frontend, PHPStan

## Sprint 8 Session Summary

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

**Phase 4** — Controller DI migration (4 batches)
- Added optional DI constructor to BaseController (DepartmentRepo, SemesterRepo, ManagerRegistry)
- Migrated all 60 controllers to constructor dependency injection
- Replaced `getDoctrine()->getRepository()` with injected repos
- Replaced `$this->get('service_id')` with injected services
- Special case: Api\PartyController extends AbstractFOSRestController

**Phase 5** — Remove BaseController bridge methods
- Removed service locator bridge methods (getDoctrine(), get(), etc.)
- Controllers now fully use constructor-injected dependencies

**Phase 6** — @Route annotations → #[Route] attributes
- Converted all 21 controllers from `@Route()` annotations to `#[Route()]` attributes
- Changed import from `Symfony\Component\Routing\Annotation\Route` to `Attribute\Route`
- No remaining @Route annotations in any controller

**Phase 7** — Config updates + remove doctrine/annotations
- routing.yml: `type: annotation` → `type: attribute`
- config.yml: `enable_annotations: true` → `enable_attributes: true` (validation + serializer)
- Removed `doctrine/annotations` ^2.0 from composer.json
- Doctrine schema validates, all routes intact (268 routes)

### Sprint 8 Success Criteria Audit

| Criterion | Status |
|-----------|--------|
| sensio/framework-extra-bundle removed | PASS |
| doctrine/annotations removed | PASS |
| 0 getDoctrine()/get() in controllers | PASS |
| 0 @ORM/@Assert annotations in entities | PASS |
| 0 @Annotation in validators | PASS |
| 0 @Route annotations in controllers | PASS |
| Doctrine config type: attribute | PASS |
| Routing config type: attribute | PASS |
| services.yml Entity/Repository autodiscovery | PASS |
| All route names preserved | PASS |
| doctrine:schema:validate | PASS |
| 496 tests, 0 failures | PASS |

### Known Issues

- `testShouldSendInfoMeetingNotification` is flaky near midnight (time-of-day dependent)
- First test run after cache clear shows failures (Twig warmup); second run is clean
- `composer update` without targeting can bump Sf packages to v7 (breaks UserRepository)
- Tests MUST use `composer test` (256M memory) — direct `php bin/phpunit` uses 128M default and fails

## Next Session

Start Sprint 9: YAML route consolidation, frontend, PHPStan.

Sprint 8 success criteria all verified — see audit below.

## Test Results (496 tests)

**0 errors + 0 failures** — All 496 tests pass (1150 assertions). Baseline tracked in `.planning/test-baseline.md`.

## Reference

- Sprint 8 plan: `.planning/phases/sprint-8/PLAN.md`
- Test commands & workflow: `docs/testing.md`
- Architecture details: `docs/architecture.md`
