# Refactoring Examples

This document provides concrete examples of refactoring controllers to use dependency injection, repositories, and extracted services.

## Example 1: ArticleController Refactoring

### Before (Current Implementation)

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;

class ArticleController extends BaseController
{
    const NUM_ARTICLES = 10;
    const NUM_CAROUSEL_ARTICLES = 5;

    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->findAllPublishedArticles();
        $departments = $em->getRepository(Department::class)->findAllDepartments();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
        ]);
    }
}
```

### After (With Dependency Injection)

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends BaseController
{
    const NUM_ARTICLES = 10;
    const NUM_CAROUSEL_ARTICLES = 5;

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

        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
        ]);
    }
}
```

### Service Configuration Update

Update `app/config/services.yml`:
```yaml
services:
    AppBundle\Controller\ArticleController:
        arguments:
            $articleRepository: '@AppBundle\Entity\Repository\ArticleRepository'
            $departmentRepository: '@AppBundle\Entity\Repository\DepartmentRepository'
            $paginator: '@knp_paginator'
```

### Update Repository to Implement Interface

```php
<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    // Existing methods remain unchanged
    // Just added implements clause
}
```

## Example 2: HomeController with Extracted Service

### Before (Current Implementation)

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Service\GeoLocation;

class HomeController extends BaseController
{
    public function showAction()
    {
        $geoLocation = $this->get(GeoLocation::class);
        $assistantsCount = count($this->getDoctrine()->getRepository(User::class)->findAssistants());
        $teamMembersCount = count($this->getDoctrine()->getRepository(User::class)->findTeamMembers());
        $articles = $this->getDoctrine()->getRepository(Article::class)->findStickyAndLatestArticles();
        
        $departments = $this->getDoctrine()->getRepository(Department::class)->findAll();
        $departmentsWithActiveAdmission = $this->getDoctrine()
            ->getRepository(Department::class)
            ->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->getDoctrine()
            ->getRepository(AssistantHistory::class)
            ->numFemale();
        $maleAssistantCount = $this->getDoctrine()
            ->getRepository(AssistantHistory::class)
            ->numMale();

        return $this->render('home/index.html.twig', [
            'assistantsCount' => $assistantsCount,
            'teamMembersCount' => $teamMembersCount,
            'articles' => $articles,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
            'ipWasLocated' => $ipWasLocated,
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
        ]);
    }
}
```

### Step 1: Create HomeService

```php
<?php

namespace AppBundle\Service;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;

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
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->geoLocation = $geoLocation;
    }

    public function getHomePageData(): array
    {
        $departments = $this->departmentRepository->findAll();
        $departmentsWithActiveAdmission = $this->departmentRepository->findAllWithActiveAdmission();
        
        return [
            'assistantsCount' => count($this->userRepository->findAssistants()),
            'teamMembersCount' => count($this->userRepository->findTeamMembers()),
            'articles' => $this->articleRepository->findStickyAndLatestArticles(),
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $this->geoLocation->sortDepartmentsByDistanceFromClient(
                $departmentsWithActiveAdmission
            ),
            'closestDepartment' => $this->geoLocation->findNearestDepartment($departments),
            'ipWasLocated' => $this->geoLocation->findCoordinatesOfCurrentRequest(),
            'femaleAssistantCount' => $this->assistantHistoryRepository->numFemale(),
            'maleAssistantCount' => $this->assistantHistoryRepository->numMale(),
        ];
    }
}
```

### Step 2: Create HomeServiceInterface

```php
<?php

namespace AppBundle\Service\Contract;

interface HomeServiceInterface
{
    public function getHomePageData(): array;
}
```

### Step 3: Update HomeController

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Service\Contract\HomeServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    private $homeService;

    public function __construct(HomeServiceInterface $homeService)
    {
        $this->homeService = $homeService;
    }

    public function showAction(): Response
    {
        $data = $this->homeService->getHomePageData();

        return $this->render('home/index.html.twig', $data);
    }
}
```

## Example 3: UserController Partner Logic Extraction

### Before (Current Implementation)

```php
public function myPartnerAction()
{
    if (!$this->getUser()->isActive()) {
        throw $this->createAccessDeniedException();
    }
    
    $activeAssistantHistories = $this->getDoctrine()
        ->getRepository(AssistantHistory::class)
        ->findActiveAssistantHistoriesByUser($this->getUser());
    
    if (empty($activeAssistantHistories)) {
        throw $this->createNotFoundException();
    }

    $partnerInformations = [];
    $partnerCount = 0;

    foreach ($activeAssistantHistories as $activeHistory) {
        $schoolHistories = $this->getDoctrine()
            ->getRepository(AssistantHistory::class)
            ->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
        $partners = [];

        foreach ($schoolHistories as $sh) {
            if ($sh->getUser() === $this->getUser()) {
                continue;
            }
            if ($sh->getDay() !== $activeHistory->getDay()) {
                continue;
            }
            if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
                $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                $partners[] = $sh;
                $partnerCount++;
            }
        }
        $partnerInformations[] = [
            'school' => $activeHistory->getSchool(),
            'assistantHistory' => $activeHistory,
            'partners' => $partners,
        ];
    }

    $semester = $this->getCurrentSemester();
    return $this->render('user/my_partner.html.twig', [
        'partnerInformations' => $partnerInformations,
        'partnerCount' => $partnerCount,
        'semester' => $semester,
    ]);
}
```

### After: Extract PartnerService

**Create PartnerService:**
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

        if (empty($activeHistories)) {
            return [
                'partnerInformations' => [],
                'partnerCount' => 0,
            ];
        }

        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeHistories as $activeHistory) {
            $partners = $this->findPartnersForHistory($activeHistory, $user);
            
            if (!empty($partners)) {
                $partnerInformations[] = [
                    'school' => $activeHistory->getSchool(),
                    'assistantHistory' => $activeHistory,
                    'partners' => $partners,
                ];
                $partnerCount += count($partners);
            }
        }

        return [
            'partnerInformations' => $partnerInformations,
            'partnerCount' => $partnerCount,
        ];
    }

    private function findPartnersForHistory($activeHistory, User $user): array
    {
        $schoolHistories = $this->assistantHistoryRepository
            ->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
        
        $partners = [];

        foreach ($schoolHistories as $schoolHistory) {
            if ($schoolHistory->getUser() === $user) {
                continue;
            }
            
            if ($schoolHistory->getDay() !== $activeHistory->getDay()) {
                continue;
            }
            
            if ($this->areInSameGroup($activeHistory, $schoolHistory)) {
                $partners[] = $schoolHistory;
            }
        }

        return $partners;
    }

    private function areInSameGroup($history1, $history2): bool
    {
        return ($history1->activeInGroup(1) && $history2->activeInGroup(1)) ||
               ($history1->activeInGroup(2) && $history2->activeInGroup(2));
    }
}
```

**Update UserController:**
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

**Better: Use Constructor Injection**
```php
<?php

namespace AppBundle\Controller;

use AppBundle\Service\PartnerService;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;

class UserController extends BaseController
{
    private $partnerService;
    private $semesterRepository;

    public function __construct(
        PartnerService $partnerService,
        SemesterRepositoryInterface $semesterRepository
    ) {
        $this->partnerService = $partnerService;
        $this->semesterRepository = $semesterRepository;
    }

    public function myPartnerAction()
    {
        if (!$this->getUser()->isActive()) {
            throw $this->createAccessDeniedException();
        }
        
        $result = $this->partnerService->findPartnersForUser($this->getUser());
        
        if ($result['partnerCount'] === 0) {
            throw $this->createNotFoundException();
        }
        
        return $this->render('user/my_partner.html.twig', [
            'partnerInformations' => $result['partnerInformations'],
            'partnerCount' => $result['partnerCount'],
            'semester' => $this->semesterRepository->findOrCreateCurrentSemester(),
        ]);
    }
}
```

## Summary of Benefits

1. **Testability:** Services can be unit tested independently
2. **Reusability:** Business logic can be reused across controllers
3. **Maintainability:** Logic is organized in dedicated classes
4. **Framework Independence:** Services can be migrated to Laravel easily
5. **Dependency Injection:** Controllers are explicit about dependencies

## Next Steps

1. Start with one controller (ArticleController is good candidate)
2. Create repository interfaces
3. Refactor controller to use dependency injection
4. Write tests
5. Repeat for next controller

