# Agent Task: Refactor Controllers to Use Dependency Injection

## Objective
Replace all `$this->get()` and `$this->container->get()` calls with constructor dependency injection.

## Current Status
- ✅ **COMPLETE** - All 57 controllers now use constructor dependency injection
- ✅ All service locator calls removed from controllers
- ✅ All dependencies injected via constructor with interfaces where available
- ✅ Examples exist in `docs/REFACTORING_EXAMPLES.md`

## Task List

### Priority Order

1. **ArticleController**
   - Replace: `$this->getDoctrine()->getRepository()`
   - Replace: `$this->get('knp_paginator')`
   - Use: Repository interfaces, PaginatorInterface

2. **UserController**
   - Replace: `$this->get(ApplicationManager::class)`
   - Replace: `$this->getDoctrine()->getRepository()`
   - Use: Constructor injection

3. **HomeController**
   - Replace: `$this->get(GeoLocation::class)`
   - Replace: `$this->getDoctrine()->getRepository()`
   - Use: HomeService (after extraction), Repository interfaces

4. **FeedbackController**
   - Replace: `$this->container->get(SlackMessenger::class)`
   - Replace: `$this->getDoctrine()->getManager()`
   - Use: FeedbackService, EntityManagerInterface or repositories

5. **ControlPanelController**
   - Replace: `$this->get(SbsData::class)`
   - Replace: `$this->getDoctrine()->getRepository()`
   - Use: Constructor injection

6. **ReceiptController**
   - Replace: `$this->container->get(Sorter::class)`
   - Replace: `$this->getDoctrine()->getRepository()`
   - Use: ReceiptStatisticsService, SorterInterface

7. **All other controllers** - Replace service locator pattern

## Implementation Pattern

### Before
```php
<?php

namespace AppBundle\Controller;

class ArticleController extends BaseController
{
    public function showAction(Request $request)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllPublishedArticles();
        
        $paginator = $this->get('knp_paginator');
        // ...
    }
}
```

### After
```php
<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;

class ArticleController extends BaseController
{
    private $articleRepository;
    private $paginator;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        PaginatorInterface $paginator
    ) {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
    }

    public function showAction(Request $request)
    {
        $articles = $this->articleRepository->findAllPublishedArticles();
        // ...
    }
}
```

## Step-by-Step Process

### Step 1: Identify Dependencies
List all dependencies used in controller:
- `$this->get('service_name')` → Service dependency
- `$this->container->get('service_name')` → Service dependency
- `$this->getDoctrine()->getRepository()` → Repository dependency
- `$this->getDoctrine()->getManager()` → EntityManager dependency

### Step 2: Add Constructor
```php
public function __construct(
    Dependency1Interface $dependency1,
    Dependency2Interface $dependency2
) {
    $this->dependency1 = $dependency1;
    $this->dependency2 = $dependency2;
}
```

### Step 3: Add Property Declarations
```php
private $dependency1;
private $dependency2;
```

### Step 4: Replace Service Locator Calls
Replace:
- `$this->get(Service::class)` → `$this->service`
- `$this->getDoctrine()->getRepository(Entity::class)` → `$this->repository`

### Step 5: Update Service Configuration
Add to `app/config/services.yml`:

```yaml
services:
    AppBundle\Controller\ArticleController:
        arguments:
            $articleRepository: '@AppBundle\Entity\Repository\ArticleRepository'
            $paginator: '@knp_paginator'
```

## Special Cases

### Case 1: EntityManager Dependency
**Option A:** Inject specific repositories (preferred)
```php
public function __construct(
    ArticleRepositoryInterface $articleRepository
) {
    $this->articleRepository = $articleRepository;
}
```

**Option B:** Inject EntityManager (if needed)
```php
use Doctrine\ORM\EntityManagerInterface;

public function __construct(EntityManagerInterface $em)
{
    $this->em = $em;
}
```

### Case 2: Framework Services (Paginator, etc.)
Use interface if available:
```php
use Knp\Component\Pager\PaginatorInterface;

public function __construct(PaginatorInterface $paginator)
{
    $this->paginator = $paginator;
}
```

### Case 3: Services Without Interfaces Yet
Temporarily inject concrete class, but note for future:
```php
use AppBundle\Service\GeoLocation;

public function __construct(GeoLocation $geoLocation)
{
    $this->geoLocation = $geoLocation;
}
// TODO: Replace with GeoLocationInterface when created
```

## Common Dependencies to Replace

| Pattern | Replacement |
|---------|-------------|
| `$this->getDoctrine()->getRepository(Entity::class)` | Inject `EntityRepositoryInterface` |
| `$this->get(Service::class)` | Inject `ServiceInterface` |
| `$this->container->get('service')` | Inject service via constructor |
| `$this->get('knp_paginator')` | Inject `PaginatorInterface` |

## Acceptance Criteria

- [ ] No `$this->get()` calls in controller
- [ ] No `$this->container->get()` calls
- [ ] No `$this->getDoctrine()->getRepository()` calls
- [ ] All dependencies injected via constructor
- [ ] Service configuration updated
- [ ] Controller actions still work (manual test)
- [ ] No syntax errors

## Testing

After refactoring:
1. **Manual Testing:** Test all controller actions
2. **Syntax Check:** `php bin/console lint:container`
3. **Integration:** Verify controller can be instantiated

## Progress Tracking

After refactoring each controller:
1. Mark complete in this file
2. Test all actions manually
3. Commit: "refactor: Use dependency injection in {ControllerName}"
4. Move to next controller

## Notes

- Do one controller at a time
- Test thoroughly before moving on
- If controller has many actions, refactor all actions in same commit
- Keep constructor parameters in logical order (repositories first, then services)

## Reference

See examples in:
- `docs/REFACTORING_EXAMPLES.md`
- `docs/SERVICE_EXTRACTION_GUIDE.md`

