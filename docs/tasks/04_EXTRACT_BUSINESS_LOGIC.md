# Agent Task: Extract Business Logic from Controllers

## Objective
Extract complex business logic from controllers into dedicated service classes, following the examples in `docs/REFACTORING_EXAMPLES.md`.

## Current Status
- ✅ Examples created for ArticleController, HomeController, UserController
- ⏳ Controllers need business logic extraction

## Task List (Priority Order)

### High Priority - Complex Controllers

1. **HomeController** - Extract Statistics Logic
   - File: `src/AppBundle/Controller/HomeController.php`
   - Create: `src/AppBundle/Service/HomeService.php`
   - Interface: `src/AppBundle/Service/Contract/HomeServiceInterface.php`
   - See example in: `docs/REFACTORING_EXAMPLES.md`

2. **UserController** - Extract Partner Finding Logic
   - File: `src/AppBundle/Controller/UserController.php`
   - Create: `src/AppBundle/Service/PartnerService.php`
   - Interface: `src/AppBundle/Service/Contract/PartnerServiceInterface.php`
   - Method: `myPartnerAction()` has complex partner finding logic
   - See example in: `docs/REFACTORING_EXAMPLES.md`

3. **ArticleController** - Extract Article Listing Logic
   - File: `src/AppBundle/Controller/ArticleController.php`
   - Create: `src/AppBundle/Service/ArticleService.php`
   - Interface: `src/AppBundle/Service/Contract/ArticleServiceInterface.php`
   - Methods: Article fetching and pagination logic

4. **ReceiptController** - Extract Statistics Logic
   - File: `src/AppBundle/Controller/ReceiptController.php`
   - Create: `src/AppBundle/Service/ReceiptStatisticsService.php` (or extend existing ReceiptStatistics)
   - Extract: Receipt statistics calculation and sorting logic

5. **FeedbackController** - Extract Submission Logic
   - File: `src/AppBundle/Controller/FeedbackController.php`
   - Create: `src/AppBundle/Service/FeedbackService.php`
   - Extract: Feedback creation and Slack notification logic

### Medium Priority

6. **ControlPanelController** - Already uses services well, but could extract SBS data logic
7. **WidgetController** - Extract widget data gathering logic
8. **UserAdminController** - Extract user creation logic (partially in UserRegistration)
9. **SurveyController** - Extract survey execution logic
10. **InterviewController** - Already uses InterviewManager, review for additional extraction

## Implementation Pattern

### Step 1: Analyze Controller Method
Identify:
- Direct Doctrine repository calls → Should use injected repositories
- Complex business logic → Extract to service
- Multiple responsibilities → Split into multiple services
- Framework-specific calls (`$this->get()`, `$this->container->get()`) → Use dependency injection

### Step 2: Create Service Class
Follow this pattern:

```php
<?php

namespace AppBundle\Service;

use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;

class HomeService implements HomeServiceInterface
{
    private $userRepository;
    private $articleRepository;
    private $geoLocation;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ArticleRepositoryInterface $articleRepository,
        GeoLocationInterface $geoLocation
    ) {
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->geoLocation = $geoLocation;
    }

    public function getHomePageData(): array
    {
        // Extracted logic from HomeController::showAction()
        return [
            'assistantsCount' => count($this->userRepository->findAssistants()),
            'teamMembersCount' => count($this->userRepository->findTeamMembers()),
            // ... etc
        ];
    }
}
```

### Step 3: Create Service Interface
```php
<?php

namespace AppBundle\Service\Contract;

interface HomeServiceInterface
{
    public function getHomePageData(): array;
}
```

### Step 4: Update Controller
```php
<?php

namespace AppBundle\Controller;

use AppBundle\Service\Contract\HomeServiceInterface;

class HomeController extends BaseController
{
    private $homeService;

    public function __construct(HomeServiceInterface $homeService)
    {
        $this->homeService = $homeService;
    }

    public function showAction()
    {
        $data = $this->homeService->getHomePageData();
        return $this->render('home/index.html.twig', $data);
    }
}
```

### Step 5: Update Service Configuration
```yaml
services:
    AppBundle\Service\HomeService:
        arguments:
            $userRepository: '@AppBundle\Entity\Repository\UserRepository'
            $articleRepository: '@AppBundle\Entity\Repository\ArticleRepository'
            $geoLocation: '@AppBundle\Service\GeoLocation'
    
    AppBundle\Service\Contract\HomeServiceInterface:
        alias: AppBundle\Service\HomeService
```

## What to Extract

### ✅ Extract (Move to Service)
- Business logic calculations
- Data aggregation (statistics, counts)
- Complex data fetching and processing
- Business rules and validations
- Multi-step workflows

### ❌ Keep in Controller
- HTTP request/response handling
- Form handling (form creation, validation)
- Redirect logic
- Flash messages
- View rendering

## Acceptance Criteria

- [ ] Service class created with extracted logic
- [ ] Service interface created
- [ ] Controller uses dependency injection (constructor injection)
- [ ] Controller is simplified (ideally < 10 lines per action)
- [ ] Service configuration updated
- [ ] No `$this->get()` or `$this->container->get()` in controller
- [ ] Tests still pass (if they exist)
- [ ] Manual testing shows functionality unchanged

## Testing Strategy

After extraction:
1. **Manual Testing:** Verify controller action still works
2. **Unit Tests:** Write tests for new service
3. **Integration Tests:** Verify controller + service integration

## Common Patterns

### Pattern 1: Data Aggregation Service
**Example:** HomeService, ReceiptStatisticsService
```php
public function getAggregatedData(): array
{
    // Fetch and combine data from multiple sources
    return [
        'data1' => $this->repo1->findAll(),
        'data2' => $this->repo2->findAll(),
        'stats' => $this->calculateStats(),
    ];
}
```

### Pattern 2: Complex Query Logic
**Example:** PartnerService
```php
public function findPartnersForUser(User $user): array
{
    // Complex query logic
    // Multiple repository calls
    // Data processing
    return $result;
}
```

### Pattern 3: Workflow Service
**Example:** FeedbackService
```php
public function submitFeedback(Feedback $feedback, User $user): void
{
    $this->repository->save($feedback);
    $this->slackMessenger->notify($feedback->getSlackMessageBody());
    // Additional workflow steps
}
```

## Reference Examples

See `docs/REFACTORING_EXAMPLES.md` for complete before/after examples:
- HomeController → HomeService
- UserController (partner logic) → PartnerService
- ArticleController → ArticleService (pattern)

## Progress Tracking

After completing each extraction:
1. Mark complete in this file
2. Test the change manually
3. Commit: "refactor: Extract {LogicName} from {Controller} to {Service}"
4. Move to next controller

## Notes

- Start with one method/action at a time
- Ensure functionality works before moving to next
- Keep commits small and focused
- Don't extract everything at once - do incrementally

