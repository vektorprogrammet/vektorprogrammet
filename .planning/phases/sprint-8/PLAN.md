# Sprint 8: DI Migration + Annotations → Attributes

## Context

Sprints 1-7b completed Symfony 3.4 → 6.4 upgrade with bridge methods in BaseController. All 496 tests pass (baseline: `.planning/test-baseline.md`). Now modernize to Sf6.4 best practices: constructor DI, PHP 8 attributes, remove deprecated/abandoned packages.

## Deprecation Status (Verified)

| Pattern | Status | Required? |
|---------|--------|-----------|
| `sensio/framework-extra-bundle` | **UNMAINTAINED** (README warning) | YES remove |
| `doctrine/annotations` | **ABANDONED** + deprecated in Sf6.4 | YES remove |
| `getDoctrine()` / `$this->get()` | Removed from AbstractController in Sf6 (bridge methods added) | YES migrate |
| `@ORM\*` / `@Assert\*` annotations | doctrine/annotations abandoned | YES → attributes |
| YAML `type: annotation` routing loader | **DEPRECATED** in Sf6.4 | YES → `type: attribute` |
| `@Route` Annotation class | NOT deprecated (intentional bridge) | YES → attributes (to enable `type: attribute`) |
| `EntityRepository` base class | NOT deprecated | YES convert (prerequisite for controller DI) |
| YAML route definitions | NOT deprecated, fully supported | NO — defer to Sprint 9 |

## Verified Metrics

| Item | Count | Scope |
|------|-------|-------|
| `getDoctrine()` calls | 260 | 55 controllers |
| `$this->get()` calls | 149 | 40 controllers |
| `@ORM\` annotations | 614 | 48 entities |
| `@Assert\` annotations | 155 | 36 entities |
| `@Route` annotations | 49 | 21 controllers |
| YAML routes | ~193 | config/routing.yml (NOT deprecated — defer) |
| Repository classes | **37** | 36 in src/App/Entity/Repository/ + 1 stray in Entity/ |
| Total controllers | 62 | **60 extend BaseController** + BaseController itself + 1 extends AbstractFOSRestController |
| Custom validators | **5** | src/App/Validator/Constraints/ using `@Annotation` |
| Entities with duplicate @ORM\Entity | **7** | Must deduplicate before attribute conversion |

## Key Architecture

- **BaseController** (`src/App/Controller/BaseController.php`): extends AbstractController, `getSubscribedServices()` returns ~30 services, bridge methods `getDoctrine()` + `get()`, helper methods `getDepartment()`/`getSemester()`/`getCurrentSemester()`
- **All repos** extend old `EntityRepository` — must convert to `ServiceEntityRepository` for autowiring
- **UserRepository** also implements `UserProviderInterface` — needs careful constructor handling
- **Api\PartyController** extends `AbstractFOSRestController` (NOT BaseController) — needs separate DI pattern
- **No controllers** currently use constructor injection
- **Routing**: hybrid — 21 controllers use `@Route` annotations, ~40 use YAML in `config/routing.yml`
- **Twig templates** use `path('route_name')` — route names MUST be preserved exactly
- **services.yml** autodiscovery does NOT include `Entity/Repository/` — must add for autowiring
- **5 custom validator constraints** use `@Annotation` docblock — must convert to `#[Attribute]`
- **2 orphan repos**: `AdmissionRepository` (references nonexistent `ApplicationStatistic` entity), `OpptakRepository` (empty, no entity) — likely dead code

## Dependency Graph

```
Phase 1 (sensio removal) ───┐
                             │
Phase 2 (repos → SER +      │
         services.yml) ─────┼──> Phase 4 (controller DI, 5 batches)
                             │         │
Phase 3 (entity attrs +     │         v
         custom validators) ─┤   Phase 5 (remove bridge methods)
                             │
Phase 6 (@Route → attrs) ───┤
                             v
                    Phase 7 (config + remove doctrine/annotations)
```

Phases 3 and 6 are **independent** of Phase 4 — can run in any order or parallel.

---

## Phase 1: Remove sensio/framework-extra-bundle

**Files**: 3 | **Risk**: Low
**Verified**: 0 sensio annotations used in source code.

1. `composer.json` — remove `"sensio/framework-extra-bundle": "^6.0"`
2. `config/bundles.php` — remove `SensioFrameworkExtraBundle::class => ['all' => true]`
3. `config/config.yml` — remove `sensio_framework_extra:` block (lines 256-258)
4. Run `composer update --no-scripts` then `bin/console cache:clear`

**Verify**: full test suite 496/0
**Commit**: "Remove unused sensio/framework-extra-bundle"

---

## Phase 2: Convert repositories to ServiceEntityRepository

**Files**: ~37 repos + services.yml | **Risk**: Medium

### Step 2a: Investigate orphan repos

Check if `AdmissionRepository` and `OpptakRepository` are used anywhere. If dead code, delete them. If used, determine which entity they serve.

### Step 2b: Move stray repo

Move `src/App/Entity/TeamInterestRepository.php` → `src/App/Entity/Repository/TeamInterestRepository.php`. Update namespace. Update `TeamInterest.php` entity `repositoryClass` from `"TeamInterestRepository"` to `"App\Entity\Repository\TeamInterestRepository"` (in the same commit).

### Step 2c: Convert all repos

All repos in `src/App/Entity/Repository/` extend `Doctrine\ORM\EntityRepository`. Convert to:
```php
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Foo;

class FooRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Foo::class);
    }
}
```

**Special cases**:
- `UserRepository` implements `UserProviderInterface` — keep interface, add constructor
- Repos using `$this->getEntityManager()` (e.g., `SemesterRepository:64-65`) — still works with ServiceEntityRepository

### Step 2d: Update services.yml for autowiring

Add repository autodiscovery to `config/services.yml`:
```yaml
App\Entity\Repository\:
    resource: "../src/App/Entity/Repository/"
```

**Can split into 2 sessions** (~18 files each).

**Verify**: `bin/console cache:clear` + `bin/console debug:container --tag=doctrine.repository_service` + full suite 496/0
**Commit**: "Convert repositories from EntityRepository to ServiceEntityRepository"

---

## Phase 3: Entity annotations → PHP 8 attributes + custom validators

**Files**: 48 entities + 5 validators | **Risk**: Medium | **3 batches of ~16 entities + 1 validator batch**

**Independent of DI work — can run before, after, or parallel with Phase 4.**

### Step 3a: Convert custom validator constraints (do first)

5 files in `src/App/Validator/Constraints/`:
- `UniqueCompanyEmail.php`
- `VektorEmail.php`
- `ApplicationEmail.php`
- `InterviewAnswer.php`
- `InfoMeeting.php`

Pattern:
```php
// Before
/** @Annotation */
class UniqueCompanyEmail extends Constraint { }

// After
#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueCompanyEmail extends Constraint { }
```

### Step 3b-d: Convert entity annotations (3 batches of ~16)

Convert patterns:
```php
// @ORM\Entity(repositoryClass="App\Entity\Repository\FooRepository")
// →
use App\Entity\Repository\FooRepository;
#[ORM\Entity(repositoryClass: FooRepository::class)]

// @ORM\Column(type="string", nullable=true)
// →
#[ORM\Column(type: "string", nullable: true)]

// @Assert\NotBlank(message="...")
// →
#[Assert\NotBlank(message: "...")]

// @UniqueEntity(fields={"email"}, message="...")
// →
#[UniqueEntity(fields: ["email"], message: "...")]

// @CustomAssert\UniqueCompanyEmail
// →
#[CustomAssert\UniqueCompanyEmail]
```

**CRITICAL — deduplicate first**: 7 entities have duplicate `@ORM\Entity` annotations (bare + repositoryClass). Having two `#[ORM\Entity]` attributes causes a PHP fatal error. Merge into single attribute:
- `StaticContent.php`, `Role.php`, `Survey.php`, `SurveyTaken.php`, `Semester.php`, `SocialEvent.php` + check others
- Keep the one with `repositoryClass`, remove the bare one

**IMPORTANT — add `use` imports**: Entity `repositoryClass` annotations use string paths. Attribute syntax uses `::class`, which requires adding `use App\Entity\Repository\FooRepository;` to each entity file (~35 entities with repositoryClass).

**@UniqueEntity** — 6 entities need special attention: `User.php` (2), `SurveyNotification.php`, `AdmissionSubscriber.php`, `Team.php`, `Department.php`. Import: `use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;`

After ALL entities converted: update `config/config.yml` line 116: `type: annotation` → `type: attribute`

**Verify per batch**: full suite 496/0
**Verify final**: `bin/console doctrine:schema:validate`
**Commits**: "Convert custom validators from @Annotation to #[Attribute]" + 3 entity batch commits + "Switch Doctrine mapping to attribute type"

---

## Phase 4: Controller DI migration

**Files**: 60 child controllers + BaseController | **Risk**: High | **5 batches (0-4)**

### Migration pattern (standard — extends BaseController)

```php
class FooController extends BaseController {
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }
}
```

### Migration pattern (special — Api\PartyController extends AbstractFOSRestController)

```php
class PartyController extends AbstractFOSRestController {
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
    ) {}
    // No parent::__construct() — AbstractFOSRestController has no constructor
    // No getDepartment/getSemester helpers available
}
```

### String ID → interface mapping
| `$this->get('...')` | Constructor type |
|---------------------|-----------------|
| `'event_dispatcher'` | `EventDispatcherInterface` |
| `'request_stack'` | `RequestStack` |
| `'knp_paginator'` | `PaginatorInterface` |
| `'kernel'` | `KernelInterface` |
| `'form.factory'` | `FormFactoryInterface` |
| `'security.token_storage'` | `TokenStorageInterface` |
| `'security.password_hasher'` | `UserPasswordHasherInterface` |
| `'security.authentication_utils'` | `AuthenticationUtils` |
| `'security.authorization_checker'` | `AuthorizationCheckerInterface` |

### BaseController constructor strategy

BaseController gets a constructor with **optional** args (default null) so child controllers can be migrated incrementally:
```php
class BaseController extends AbstractController {
    private ?DepartmentRepository $departmentRepo;
    private ?SemesterRepository $semesterRepo;

    public function __construct(
        ?DepartmentRepository $departmentRepo = null,
        ?SemesterRepository $semesterRepo = null,
    ) {
        $this->departmentRepo = $departmentRepo;
        $this->semesterRepo = $semesterRepo;
    }

    public function getDepartment(Request $request): ?Department {
        // Use injected repo if available, fall back to getDoctrine() bridge
        $repo = $this->departmentRepo ?? $this->getDoctrine()->getRepository(Department::class);
        // ...
    }
}
```

This allows incremental migration — child controllers can be updated batch by batch. Once ALL are done, Phase 5 removes the fallback.

### Execution order

**Batch 0 — BaseController**: Add optional constructor. Update helper methods with fallback. Keep bridge methods alive.

**Batch 1** — 14 controllers (highest call counts): AdmissionAdminController, InterviewController, SchoolAdminController, TeamAdminController, SurveyController, ProfileController, ReceiptController, HomeController, WidgetController, ArticleAdminController, AccessRuleController, PasswordResetController, ExecutiveBoardController, AssistantController

**Batch 2** — 14 controllers: UserAdminController, StandController, CertificateController, ContactController, SponsorsController, SocialEventController, SubstituteController, ChangeLogController, DepartmentController, MailingListController, SemesterController, AdmissionPeriodController, SchoolCapacityController, FeedbackController

**Batch 3** — 14 controllers: UserController, TeamApplicationController, SurveyNotifierController, BoardAndTeamController, AdmissionSubscriberController, ArticleController, AssistantSchedulingController, UserGroupCollectionController, ParticipantHistoryController, AssistantHistoryController, InterviewSchemaController, ProfilePhotoController, PositionController, TeamInterestController

**Batch 4** — remaining ~18: SurveyPopupController, ControlPanelController, ExistingUserAdmissionController, TeamController, SecurityController, SsoController, GitHubController, SignatureController, ApplicationStatisticsController, StaticContentController, FieldOfStudyController, ConfirmationController, FrontEndController, FileBrowserController, AboutVektorController, TeacherController, ParentsController, Api/AccountController
+ **Special**: Api/PartyController (extends AbstractFOSRestController — no parent::__construct)

**Verify per batch**: `bin/phpunit --filter=RelevantTest` then full suite 496/0
**Commits**: "Add constructor DI to BaseController helpers" + "Migrate controllers to constructor DI (batch N)"

---

## Phase 5: Remove BaseController bridge methods

**Files**: 1 | **Risk**: Low (if Phase 4 complete)

In `src/App/Controller/BaseController.php`:
1. Make constructor args required (remove `?` and `= null`)
2. Delete `getDoctrine()` method
3. Delete `get()` method
4. Remove fallback logic in helper methods (use injected repos directly)
5. Remove all entries from `getSubscribedServices()` that are now constructor-injected
6. Grep entire `src/` for remaining `getDoctrine(` or `$this->get(` — should be 0

**Verify**: full suite 496/0
**Commit**: "Remove BaseController bridge methods and trim service locator"

---

## Phase 6: @Route annotations → #[Route] attributes

**Files**: 21 controllers | **Risk**: Low

Change import and syntax in all 21 controllers that use `@Route`:
```php
// Before
use Symfony\Component\Routing\Annotation\Route;
/** @Route("/path", name="name", methods={"GET"}) */

// After
use Symfony\Component\Routing\Attribute\Route;
#[Route("/path", name: "name", methods: ["GET"])]
```

Controllers: AccessRuleController, Api/AccountController, AdmissionAdminController, AdmissionSubscriberController, ArticleAdminController, AssistantController, CertificateController, ConfirmationController, ContactController, ExecutiveBoardController, ExistingUserAdmissionController, InterviewController, Api/PartyController, SemesterController, SponsorsController, StandController, SurveyController, TeamAdminController, TeamApplicationController, TeamInterestController, UserController

**Verify**: `bin/console debug:router | wc -l` unchanged + full suite 496/0
**Commit**: "Convert @Route annotations to #[Route] attributes"

---

## Phase 7: Config updates + remove doctrine/annotations

**Files**: 2-3 | **Risk**: Low (if Phases 3+6 complete)

1. Update `config/routing.yml`: change `type: annotation` → `type: attribute`
2. Remove `doctrine/annotations` from `composer.json`
3. Run `composer update`
4. If `framework.annotations` config exists, set to `false` or remove

**Verify**: `bin/console cache:clear` + `bin/console doctrine:schema:validate` + `bin/console debug:router` + full suite 496/0
**Commit**: "Switch routing to attribute loader, remove doctrine/annotations"

---

## Deferred to Sprint 9 — Status

- **YAML routes → #[Route] attributes**: DONE (Sprint 9, commit `f7add72a`). ~193 routes migrated to 48 controllers.
- **Frontend modernization**: NOT STARTED
- **PHPStan level increase**: NOT STARTED

---

## Execution Strategy

**Recommended sequential order**:
1. Phase 1 (sensio) — 1 session
2. Phase 2 (repos + services.yml) — 2 sessions
3. Phase 3 (validators + entity attrs) — 3 sessions
4. Phase 4 (controller DI) — 5 sessions (biggest phase)
5. Phase 5 (bridge removal) — 1 session
6. Phase 6 (@Route attrs) — 1 session
7. Phase 7 (cleanup) — 1 session

**Total**: ~14 sessions, ~14 commits

## Agent Resumption Protocol

Each agent session should:
1. Read `.planning/STATE.md` for current progress
2. Read `.planning/phases/sprint-8/PLAN.md` (this plan) for phase details
3. Run `bin/phpunit` to verify baseline (496 tests, 0 failures)
4. Execute the next incomplete phase/batch
5. Run full test suite after changes
6. Commit with descriptive message
7. Update `.planning/STATE.md` with completed phase + next step

## Key Files Reference

| File | Why |
|------|-----|
| `src/App/Controller/BaseController.php` | Bridge methods, DI pattern setter, helper methods |
| `src/App/Controller/Api/PartyController.php` | Extends AbstractFOSRestController, NOT BaseController — special DI pattern |
| `src/App/Entity/Repository/UserRepository.php` | Complex repo (UserProviderInterface), template for conversion |
| `src/App/Entity/User.php` | Largest entity (29 @ORM + 12 @Assert + 2 @UniqueEntity) |
| `src/App/Entity/StaticContent.php` | Example of duplicate @ORM\Entity — must deduplicate |
| `src/App/Validator/Constraints/UniqueCompanyEmail.php` | Example custom validator needing @Annotation → #[Attribute] |
| `config/config.yml:116` | Doctrine `type: annotation` → `type: attribute` |
| `config/config.yml:256-258` | sensio_framework_extra config to remove |
| `config/routing.yml` | `type: annotation` → `type: attribute` |
| `config/services.yml` | Must add Entity/Repository autodiscovery for repo autowiring |
| `config/bundles.php` | sensio bundle registration |
| `.planning/test-baseline.md` | Test count baseline |
| `.planning/STATE.md` | Progress tracking |

## Success Criteria — ALL COMPLETE

- [x] `sensio/framework-extra-bundle` removed from project
- [x] `doctrine/annotations` removed from project
- [x] 0 calls to `getDoctrine()` or `$this->get()` in controllers
- [x] 0 `@ORM\*` or `@Assert\*` annotations in entities
- [x] 0 `@Annotation` docblocks in custom validators
- [x] 0 `@Route` annotations in controllers (all use `#[Route]` attributes)
- [x] Doctrine config uses `type: attribute`
- [x] Routing config uses `type: attribute`
- [x] `services.yml` includes Entity/Repository autodiscovery
- [x] All 496 tests pass, 0 failures
- [x] `bin/console doctrine:schema:validate` passes
- [x] All route names preserved (templates work unchanged)
