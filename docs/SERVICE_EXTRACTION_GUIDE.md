# Service Extraction & Modernization Guide

This document outlines the strategy for extracting business logic from controllers, creating service interfaces, and implementing the repository pattern to prepare for Laravel migration.

## Current Issues Identified

### 1. Direct Doctrine Access in Controllers

**Problem:** Controllers directly access Doctrine repositories:

```php
$articles = $this->getDoctrine()->getRepository(Article::class)->findAllPublishedArticles();
$admissionPeriod = $this->getDoctrine()
    ->getRepository(AdmissionPeriod::class)
    ->findOneByDepartmentAndSemester($department, $semester);
```

**Solution:** Create repository classes/interfaces and inject them via constructor.

### 2. Container Access (`$this->get()`, `$this->container->get()`)

**Problem:** Controllers use service locator pattern:

```php
$geoLocation = $this->get(GeoLocation::class);
$messenger = $this->container->get(SlackMessenger::class);
```

**Solution:** Use constructor dependency injection.

### 3. Business Logic in Controllers

**Problem:** Complex business logic resides in controllers:

- Example: `UserController::myPartnerAction()` has complex partner finding logic
- Example: `ArticleController` has pagination logic mixed with view rendering

**Solution:** Extract to service classes.

### 4. No Service Interfaces

**Problem:** Services are concrete classes, making testing and swapping implementations difficult.

**Solution:** Create interfaces for all services.

### 5. Missing Repository Pattern

**Problem:** Repository logic is mixed with controllers and entity repositories.

**Solution:** Create explicit repository interfaces and implementations.

## Refactoring Strategy

### Phase 1: Repository Pattern Implementation

#### Step 1: Create Repository Interfaces

Create interfaces for major entity repositories in `src/AppBundle/Repository/Contract/`:

**Example: ArticleRepositoryInterface**

```php
<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Article;
use AppBundle\Entity\Department;

interface ArticleRepositoryInterface
{
    public function findAllPublishedArticles(): array;
    public function findAllArticlesByDepartments($department): array;
    public function findLatestArticles(int $limit, ?int $excludeId = null): array;
    public function findStickyAndLatestArticles(?int $limit = null): array;
    public function findLatestArticlesByDepartment($departmentId, int $limit): array;
}
```

**Entities needing repository interfaces:**

1. ArticleRepository
2. UserRepository
3. DepartmentRepository
4. AdmissionPeriodRepository
5. ApplicationRepository
6. InterviewRepository
7. SurveyRepository
8. ReceiptRepository
9. TeamRepository
10. SchoolRepository
11. AssistantHistoryRepository
12. SemesterRepository

#### Step 2: Implement Repository Pattern in Existing Repositories

Update existing Doctrine repositories to implement interfaces:

```php
<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Article;
use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    // Existing methods already exist
    // Just add implements clause
}
```

#### Step 3: Inject Repositories in Controllers

Refactor controllers to use constructor injection:

**Before:**

```php
class ArticleController extends BaseController
{
    public function showAction(Request $request)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllPublishedArticles();
        // ...
    }
}
```

**After:**

```php
class ArticleController extends BaseController
{
    private $articleRepository;
    private $departmentRepository;
    private $paginator;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        DepartmentRepositoryInterface $departmentRepository,
        PaginatorInterface $paginator
    ) {
        $this->articleRepository = $articleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->paginator = $paginator;
    }

    public function showAction(Request $request)
    {
        $articles = $this->articleRepository->findAllPublishedArticles();
        $departments = $this->departmentRepository->findAllDepartments();
        // ...
    }
}
```

### Phase 2: Service Interface Creation

#### Create Interfaces for Existing Services

**Location:** `src/AppBundle/Service/Contract/`

**Priority Services for Interface Creation:**

1. **UserServiceInterface**

```php
<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\User;

interface UserServiceInterface
{
    public function createUser(array $data): User;
    public function updateUser(User $user, array $data): void;
    public function activateUser(User $user, string $code): bool;
    // ... other methods
}
```

2. **ApplicationManagerInterface**
3. **InterviewManagerInterface**
4. **SurveyManagerInterface**
5. **AdmissionNotifierInterface**
6. **MailerInterface** (already exists)
7. **SmsSenderInterface** (already exists)
8. **FileUploaderInterface**
9. **RoleManagerInterface**
10. **GeoLocationInterface**

#### Update Service Implementations

```php
<?php

namespace AppBundle\Service;

use AppBundle\Service\Contract\UserServiceInterface;
use AppBundle\Entity\User;

class UserService implements UserServiceInterface
{
    // Existing implementation
}
```

#### Update Service Configuration

Update `app/config/services.yml`:

```yaml
services:
    AppBundle\Service\Contract\UserServiceInterface: '@AppBundle\Service\UserService'
    AppBundle\Service\Contract\ApplicationManagerInterface: '@AppBundle\Service\ApplicationManager'
    # ... etc
```

### Phase 3: Extract Business Logic from Controllers

#### Example: UserController Partner Logic

**Before:**

```php
public function myPartnerAction()
{
    // Complex business logic here
    $partnerInformations = [];
    $partnerCount = 0;
    
    foreach ($activeAssistantHistories as $activeHistory) {
        $schoolHistories = $this->getDoctrine()
            ->getRepository(AssistantHistory::class)
            ->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
        // ... more logic
    }
    // ...
}
```

**After - Create PartnerService:**

```php
<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;

class PartnerService
{
    private $assistantHistoryRepository;

    public function __construct(AssistantHistoryRepositoryInterface $repository)
    {
        $this->assistantHistoryRepository = $repository;
    }

    public function findPartnersForUser(User $user): array
    {
        $activeHistories = $this->assistantHistoryRepository
            ->findActiveAssistantHistoriesByUser($user);
        
        $partnerInformations = [];
        $partnerCount = 0;
        
        foreach ($activeHistories as $activeHistory) {
            $partners = $this->findPartnersForHistory($activeHistory, $user);
            // ... logic
        }
        
        return [
            'partnerInformations' => $partnerInformations,
            'partnerCount' => $partnerCount,
        ];
    }

    private function findPartnersForHistory($activeHistory, User $user): array
    {
        // Extracted logic
    }
}
```

**Updated Controller:**

```php
public function myPartnerAction()
{
    if (!$this->getUser()->isActive()) {
        throw $this->createAccessDeniedException();
    }
    
    $result = $this->get(PartnerService::class)->findPartnersForUser($this->getUser());
    
    if ($result['partnerCount'] === 0) {
        throw $this->createNotFoundException();
    }
    
    return $this->render('user/my_partner.html.twig', [
        'partnerInformations' => $result['partnerInformations'],
        'partnerCount' => $result['partnerCount'],
        'semester' => $this->getCurrentSemester(),
    ]);
}
```

#### Example: HomeController Statistics

**Extract to HomeService:**

```php
<?php

namespace AppBundle\Service;

class HomeService
{
    private $userRepository;
    private $articleRepository;
    private $departmentRepository;
    private $assistantHistoryRepository;
    private $geoLocation;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ArticleRepositoryInterface $articleRepository,
        DepartmentRepositoryInterface $departmentRepository,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        GeoLocationInterface $geoLocation
    ) {
        // ... assignments
    }

    public function getHomePageData(): array
    {
        return [
            'assistantsCount' => count($this->userRepository->findAssistants()),
            'teamMembersCount' => count($this->userRepository->findTeamMembers()),
            'articles' => $this->articleRepository->findStickyAndLatestArticles(),
            'departments' => $this->departmentRepository->findAll(),
            'departmentsWithActiveAdmission' => $this->getSortedDepartments(),
            'closestDepartment' => $this->geoLocation->findNearestDepartment($departments),
            'femaleAssistantCount' => $this->assistantHistoryRepository->numFemale(),
            'maleAssistantCount' => $this->assistantHistoryRepository->numMale(),
            'ipWasLocated' => $this->geoLocation->findCoordinatesOfCurrentRequest(),
        ];
    }
}
```

### Phase 4: Dependency Injection in Controllers

#### Update BaseController

Make BaseController framework-agnostic where possible:

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;

abstract class BaseController extends Controller
{
    protected function getDepartmentRepository(): DepartmentRepositoryInterface
    {
        return $this->get(DepartmentRepositoryInterface::class);
    }

    protected function getSemesterRepository(): SemesterRepositoryInterface
    {
        return $this->get(SemesterRepositoryInterface::class);
    }

    // Refactor helper methods to use repositories
    public function getDepartment(Request $request): ?Department
    {
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($this->getUser() !== null) {
                return $this->getUser()->getDepartment();
            }
        } else {
            return $this->getDepartmentRepository()->find($departmentId);
        }
        return null;
    }
}
```

#### Migrate Controllers to Constructor Injection

**Priority Controllers for Refactoring:**

1. **ArticleController** - Extract article listing logic
2. **UserController** - Extract partner finding, application status logic
3. **HomeController** - Extract statistics gathering
4. **ControlPanelController** - Already uses services well
5. **InterviewController** - Already uses InterviewManager, good example
6. **FeedbackController** - Extract feedback submission logic
7. **ReceiptController** - Extract receipt statistics logic

### Phase 5: Create Framework-Agnostic Services

#### Service Design Principles

1. **No Framework Dependencies** - Services should not depend on Symfony-specific classes
2. **Use Interfaces** - All dependencies should be interfaces
3. **Return Plain Objects** - Return entities/arrays, not framework responses
4. **Throw Generic Exceptions** - Use domain exceptions, not framework exceptions

#### Example: Framework-Agnostic Service

```php
<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\MailerInterface;

class UserRegistration
{
    private $userRepository;
    private $mailer;
    private $passwordManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MailerInterface $mailer,
        PasswordManagerInterface $passwordManager
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->passwordManager = $passwordManager;
    }

    public function registerUser(array $userData): User
    {
        $user = new User();
        // ... setup user
        
        $this->userRepository->save($user);
        $this->sendActivationCode($user);
        
        return $user;
    }

    private function sendActivationCode(User $user): void
    {
        $code = $this->generateActivationCode();
        $user->setActivationCode($code);
        $this->userRepository->save($user);
        
        $this->mailer->send(
            $user->getEmail(),
            'Activation Code',
            'Your activation code is: ' . $code
        );
    }
}
```

## Refactoring Checklist

### Repository Pattern

- [ ] Create repository interfaces for 12+ major entities
- [ ] Update existing repositories to implement interfaces
- [ ] Update service configuration to bind interfaces
- [ ] Refactor controllers to inject repositories
- [ ] Remove direct `getDoctrine()` calls from controllers

### Service Interfaces

- [ ] Create interfaces for all 32 services
- [ ] Update service implementations to implement interfaces
- [ ] Update service configuration bindings
- [ ] Update controllers to type-hint interfaces

### Business Logic Extraction

- [ ] Extract partner finding logic → PartnerService
- [ ] Extract home page statistics → HomeService
- [ ] Extract article listing/pagination → ArticleService
- [ ] Extract receipt statistics → ReceiptStatisticsService
- [ ] Extract feedback submission → FeedbackService
- [ ] Review all controllers for extractable logic

### Dependency Injection

- [ ] Convert all `$this->get()` calls to constructor injection
- [ ] Convert all `$this->container->get()` calls
- [ ] Update BaseController to use dependency injection
- [ ] Remove service locator pattern

### Framework Decoupling

- [ ] Review services for Symfony dependencies
- [ ] Replace Symfony exceptions with domain exceptions where possible
- [ ] Ensure services return plain objects, not framework responses
- [ ] Document service contracts

## Testing Strategy

After each refactoring step:

1. Write unit tests for extracted services
2. Write integration tests for controller actions
3. Verify existing tests still pass
4. Add new tests for extracted logic

## Migration to Laravel

Once services are extracted and interfaces created:

1. Services can be reused with minimal changes
2. Repository interfaces map to Laravel repositories or model methods
3. Dependency injection patterns translate directly to Laravel
4. Business logic is already decoupled from framework

## Priority Order

### High Priority (Do First)

1. Create repository interfaces for core entities (User, Article, Application, Department)
2. Extract business logic from UserController, HomeController, ArticleController
3. Create service interfaces for critical services (UserService, ApplicationManager)
4. Refactor controllers to use constructor injection

### Medium Priority

1. Complete repository interfaces for all entities
2. Create interfaces for remaining services
3. Extract business logic from remaining controllers

### Lower Priority

1. Framework-agnostic service refactoring (can be done during Laravel migration)
2. Advanced decoupling (domain events, etc.)

## Files to Create

### Repository Interfaces

- `src/AppBundle/Repository/Contract/ArticleRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/UserRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/DepartmentRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/AdmissionPeriodRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/ApplicationRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/InterviewRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/SurveyRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/ReceiptRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/TeamRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/SchoolRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/AssistantHistoryRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/SemesterRepositoryInterface.php`

### Service Interfaces (New)

- `src/AppBundle/Service/Contract/UserServiceInterface.php` (if not exists)
- `src/AppBundle/Service/Contract/ApplicationManagerInterface.php`
- `src/AppBundle/Service/Contract/InterviewManagerInterface.php`
- `src/AppBundle/Service/Contract/SurveyManagerInterface.php`
- `src/AppBundle/Service/Contract/PartnerServiceInterface.php`
- `src/AppBundle/Service/Contract/HomeServiceInterface.php`
- `src/AppBundle/Service/Contract/ArticleServiceInterface.php`
- `src/AppBundle/Service/Contract/FeedbackServiceInterface.php`

### New Services to Create

- `src/AppBundle/Service/PartnerService.php`
- `src/AppBundle/Service/HomeService.php`
- `src/AppBundle/Service/ArticleService.php`
- `src/AppBundle/Service/FeedbackService.php`

## Next Steps

1. Start with repository interfaces for top 5 entities
2. Extract one controller's business logic as proof of concept
3. Create service interfaces for services used by that controller
4. Refactor that controller to use dependency injection
5. Write tests
6. Repeat for next controller/service group
