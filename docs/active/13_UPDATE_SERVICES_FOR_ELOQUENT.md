# Agent Task: Update Services to Use Eloquent Repositories

## Objective
Update all Laravel service implementations to use Eloquent repositories instead of Doctrine EntityManager, enabling full Laravel migration.

## Current Status
- ✅ All 56 services migrated to Laravel
- ✅ All 32 Eloquent repositories implemented
- ⏳ **Eloquent models in progress** - 15/60+ models created
- ⏳ **Services conversion in progress** - 6/32 services converted
- **Progress:** ~19% (6 services converted, 26 remaining)

---

## Prerequisites

**THIS TASK CANNOT START UNTIL:**
- ✅ Task 12 (Create Eloquent Models) is complete
- ✅ All required Eloquent models exist
- ✅ Eloquent repositories are fully functional

**Check if ready:**
- Verify models exist in `laravel-app/app/Models/`
- Verify repositories can use models successfully
- Run repository tests to confirm functionality

---

## Service Groups (For Parallel Work)

### Group A: Core Services (10-15 services)
**High-priority services used by many parts of the application:**
- UserService
- AdmissionService
- ApplicationService
- AuthenticationService (if exists)
- AuthorizationService (if exists)

### Group B: Admin Services (10-15 services)
**Services for admin/management functionality:**
- UserManagementService
- AdmissionAdminService
- SchoolManagementService
- ArticleManagementService
- DepartmentManagementService
- SemesterManagementService
- FieldOfStudyManagementService
- ExecutiveBoardManagementService
- AdmissionPeriodManagementService

### Group C: Supporting Services (20-30 services)
**Business logic and supporting services:**
- HomeService
- PartnerService
- ArticleService
- InterviewSchedulingService
- ProfileService
- TeamAdminService
- ReceiptStatisticsService
- FeedbackSubmissionService
- CertificateService
- LogService
- FilterService
- AccessControlService
- TeamMembershipService
- SurveyExecutionService
- And remaining services

**This task can be split across multiple agents working in parallel.**

---

## Conversion Pattern

### Before (Using Doctrine EntityManager)

```php
<?php

namespace App\Service;

use App\Service\Contract\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements UserServiceInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->setEmail($data['email']);
        // ... set other properties
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }

    public function updateUser(User $user, array $data): void
    {
        $user->setEmail($data['email']);
        // ... update other properties
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->findUserById($id);
    }
}
```

### After (Using Eloquent Repositories Only)

```php
<?php

namespace App\Service;

use App\Service\Contract\UserServiceInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->email = $data['email'];
        // ... set other properties
        
        $this->userRepository->save($user);
        
        return $user;
    }

    public function updateUser(User $user, array $data): void
    {
        $user->email = $data['email'];
        // ... update other properties
        
        $this->userRepository->save($user);
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->delete($user);
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->findUserById($id);
    }
}
```

---

## Key Changes Required

### 1. Remove EntityManagerInterface Dependency

**Find:**
```php
use Doctrine\ORM\EntityManagerInterface;

public function __construct(
    EntityManagerInterface $entityManager,
    // ...
) {
    $this->entityManager = $entityManager;
}
```

**Replace with:**
- Remove `EntityManagerInterface` import
- Remove `$entityManager` from constructor
- Remove `$this->entityManager` property

### 2. Replace EntityManager Operations

#### persist() + flush() → Repository save()
**Before:**
```php
$this->entityManager->persist($entity);
$this->entityManager->flush();
```

**After:**
```php
$this->entityRepository->save($entity);
```

#### remove() + flush() → Repository delete()
**Before:**
```php
$this->entityManager->remove($entity);
$this->entityManager->flush();
```

**After:**
```php
$this->entityRepository->delete($entity);
```

#### flush() only → Repository save() or remove()
**Before:**
```php
// After multiple changes
$this->entityManager->flush();
```

**After:**
```php
// Save each entity individually
$this->entityRepository->save($entity1);
$this->entityRepository->save($entity2);
```

### 3. Replace Entity Manager Queries

**Before:**
```php
$users = $this->entityManager
    ->getRepository(User::class)
    ->findBy(['isActive' => true]);
```

**After:**
```php
$users = $this->userRepository->findBy(['is_active' => true]);
```

### 4. Update Property Access

**Doctrine (getters/setters):**
```php
$user->setEmail('email@example.com');
$email = $user->getEmail();
```

**Eloquent (direct property access):**
```php
$user->email = 'email@example.com';
$email = $user->email;
```

### 5. Update Collection Access

**Doctrine:**
```php
$roles = $user->getRoles()->toArray();
$user->getRoles()->add($role);
```

**Eloquent:**
```php
$roles = $user->roles->toArray();
$user->roles()->attach($roleId); // For many-to-many
$user->roles()->save($role); // For one-to-many
```

---

## Step-by-Step Process

### For Each Service:

1. **Identify Dependencies**
   - List all `EntityManagerInterface` usages
   - List all repository dependencies
   - Identify entities being used

2. **Remove EntityManager**
   - Remove from constructor
   - Remove property declaration
   - Remove import statement

3. **Inject Required Repositories**
   - Add repository interfaces to constructor
   - Add repository properties
   - Ensure repositories are available (check service provider)

4. **Replace EntityManager Operations**
   - Replace `persist()` + `flush()` with `repository->save()`
   - Replace `remove()` + `flush()` with `repository->delete()`
   - Replace direct queries with repository method calls

5. **Update Property Access**
   - Replace getters/setters with direct property access
   - Update collection access patterns

6. **Test the Service**
   - Run existing tests (if any)
   - Verify service still implements interface correctly
   - Test service methods manually

7. **Verify No Doctrine Dependencies**
   - Search for `EntityManagerInterface`
   - Search for `Doctrine\ORM`
   - Ensure no Doctrine-specific code remains

---

## Common Patterns

### Pattern 1: Simple CRUD Service

**Before:**
```php
class SimpleService
{
    private EntityManagerInterface $em;
    private EntityRepositoryInterface $repo;

    public function create(Entity $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}
```

**After:**
```php
class SimpleService
{
    private EntityRepositoryInterface $repo;

    public function __construct(EntityRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function create(Entity $entity): void
    {
        $this->repo->save($entity);
    }
}
```

### Pattern 2: Service with Multiple Entities

**Before:**
```php
public function createWithRelations(User $user, Department $dept): void
{
    $this->em->persist($user);
    $this->em->persist($dept);
    $this->em->flush();
}
```

**After:**
```php
public function __construct(
    UserRepositoryInterface $userRepo,
    DepartmentRepositoryInterface $deptRepo
) {
    $this->userRepo = $userRepo;
    $this->deptRepo = $deptRepo;
}

public function createWithRelations(User $user, Department $dept): void
{
    $this->userRepo->save($user);
    $this->deptRepo->save($dept);
}
```

### Pattern 3: Complex Query Replacement

**Before:**
```php
$qb = $this->em->createQueryBuilder();
$users = $qb->select('u')
    ->from(User::class, 'u')
    ->where('u.isActive = :active')
    ->setParameter('active', true)
    ->getQuery()
    ->getResult();
```

**After:**
```php
$users = $this->userRepository->findBy(['is_active' => true]);
// Or use a custom repository method if the query is complex
$users = $this->userRepository->findActiveUsers();
```

---

## Repository Interface Methods

Most repositories should have these standard methods:

- `save($entity): void` - Save/update entity
- `delete($entity): void` - Delete entity
- `findById(int $id): ?Entity` - Find by ID
- `findAll(): array` - Find all
- `findBy(array $criteria): array` - Find by criteria

If a repository doesn't have a needed method, either:
1. Add it to the repository interface and implementation
2. Use existing repository methods
3. Ask for clarification

---

## Acceptance Criteria

For each service, verify:

- [ ] `EntityManagerInterface` dependency removed
- [ ] All `persist()` + `flush()` replaced with `repository->save()`
- [ ] All `remove()` + `flush()` replaced with `repository->delete()`
- [ ] All direct EntityManager queries replaced with repository methods
- [ ] All getters/setters replaced with direct property access (if applicable)
- [ ] Collection access updated to Eloquent patterns
- [ ] Service still implements its interface correctly
- [ ] All repository dependencies properly injected
- [ ] No Doctrine-specific code remains
- [ ] No syntax errors
- [ ] PHPStan/static analysis passes (if configured)
- [ ] Service tests pass (if they exist)
- [ ] Manual testing successful

---

## Testing Strategy

### Unit Tests (if they exist)

1. **Run existing service tests:**
   ```bash
   cd laravel-app
   php artisan test tests/Unit/Service
   ```

2. **Update test mocks:**
   - Mock repository interfaces instead of EntityManager
   - Update test expectations to match new patterns

### Manual Testing

1. **Create test script:**
   ```php
   // test_service.php
   $service = app(UserServiceInterface::class);
   $user = $service->findUserById(1);
   var_dump($user);
   ```

2. **Test each service method:**
   - Verify methods execute without errors
   - Verify data is saved/retrieved correctly
   - Verify relationships work

### Integration Testing

1. **Test service + repository + model:**
   - Create entity via service
   - Retrieve via repository
   - Verify model relationships

---

## Progress Tracking

### Services to Update (56 total)

**Group A: Core Services**
- [ ] UserService
- [ ] AdmissionService
- [ ] ApplicationService
- [ ] (Other core services as identified)

**Group B: Admin Services**
- [x] UserManagementService ✅
- [ ] AdmissionAdminService
- [ ] SchoolManagementService
- [x] ArticleManagementService ✅
- [x] DepartmentManagementService ✅
- [x] SemesterManagementService ✅
- [ ] FieldOfStudyManagementService
- [ ] ExecutiveBoardManagementService
- [ ] AdmissionPeriodManagementService

**Group C: Supporting Services**
- [ ] HomeService
- [ ] PartnerService
- [x] ArticleService ✅ (already converted - uses repositories only)
- [ ] InterviewSchedulingService
- [ ] ProfileService
- [x] TeamAdminService ✅
- [ ] ReceiptStatisticsService
- [ ] FeedbackSubmissionService
- [x] SlugMaker ✅ (dependency of ArticleManagementService)
- [ ] CertificateService
- [ ] LogService
- [ ] FilterService
- [ ] AccessControlService
- [ ] (All other services)

---

## Common Issues and Solutions

### Issue 1: Repository Method Doesn't Exist

**Problem:** Service needs a repository method that doesn't exist.

**Solution:**
1. Check repository interface for available methods
2. If method exists in Doctrine repository but not in interface, add it
3. Implement method in Eloquent repository
4. Use the new method

### Issue 2: Entity Not Found

**Problem:** Entity class reference needs to be updated.

**Solution:**
- Update imports: `AppBundle\Entity\User` → `App\Models\User`
- Update namespace references

### Issue 3: Relationship Not Working

**Problem:** Eloquent relationship not configured correctly.

**Solution:**
1. Verify model has relationship defined
2. Check foreign key names match
3. Verify pivot table configuration (for many-to-many)

### Issue 4: Collection Methods Different

**Problem:** Doctrine collection methods don't work with Eloquent.

**Solution:**
- Use Eloquent collection methods
- Example: `->toArray()`, `->first()`, `->filter()`

---

## Notes

- **Work incrementally:** Update one service, test, commit, then move to next
- **Follow interface contracts:** Don't change method signatures unless interface changes
- **Test as you go:** Don't wait until all services are updated
- **Check repository availability:** Ensure repository is bound in service provider
- **Ask questions:** If conversion pattern is unclear, ask before implementing

---

## Reference Files

### Service Examples (After Conversion)
- Check `laravel-app/app/Service/` for converted services
- Study patterns from first converted services

### Repository Interfaces
- `laravel-app/app/Repository/Contract/` - All repository interfaces
- Check available methods before implementing

### Repository Implementations
- `laravel-app/app/Repository/Eloquent/` - Eloquent repository implementations
- See how repositories work with models

### Service Provider
- `laravel-app/app/Providers/AppServiceProvider.php` - Service bindings
- `laravel-app/app/Providers/RepositoryServiceProvider.php` - Repository bindings

---

## Coding Standards

**MUST follow:** `docs/tasks/CODING_STANDARDS.md`

Key requirements:
- ✅ Type hints on all parameters
- ✅ Return types on all methods (including `void`)
- ✅ Complete PHPDoc comments
- ✅ Interface implementation matches exactly

---

## Estimated Time

- **Per Service:** 30-90 minutes (depending on complexity)
- **Group A (10-15 services):** 5-15 hours
- **Group B (10-15 services):** 5-15 hours
- **Group C (20-30 services):** 10-30 hours

**Total Estimated:** 20-60 hours for all 56 services

---

## Dependencies

**Must Complete First:**
- ✅ Task 12: Create Eloquent Models
- ✅ All required models must exist
- ✅ Repositories must be functional

**Can Work In Parallel:**
- ✅ Multiple agents can work on different service groups
- ✅ Each agent works independently on their assigned services

---

## Questions or Issues?

If you encounter:
- **Missing repository method:** Check interface, add if needed
- **Entity not found:** Verify model exists and namespace is correct
- **Relationship issues:** Check model relationship definitions
- **Complex query:** Consider adding custom repository method

---

**Status:** ⏳ In Progress (~19% complete)  
**Priority:** 🔴 High (Blocking full Laravel migration)  
**Can Work in Parallel:** ✅ Yes (split across agents by group)  
**Progress:** 6 services converted, 26 remaining with EntityManager

