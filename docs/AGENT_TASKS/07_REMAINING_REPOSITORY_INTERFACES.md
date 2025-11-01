# Agent Task: Create Remaining Repository Interfaces

## ⚠️ Important: Read Coding Standards First
**Before starting this task, read:**
- `docs/AGENT_TASKS/CODING_STANDARDS.md`

## Objective
Create repository interfaces for the 6 remaining repositories to complete the repository abstraction layer (currently 84% complete, target: 100%).

## Current Status
- ✅ 32/38 repository interfaces created (84%)
- ⏳ 6 repositories remaining without interfaces

## Task List

### 1. CertificateRequestRepository
**Entity:** `AppBundle\Entity\CertificateRequest`  
**Repository:** `src/AppBundle/Entity/Repository/CertificateRequestRepository.php`  
**Priority:** Low (minor usage)

**Steps:**
1. Read existing repository file
2. Identify all public methods
3. Create interface at: `src/AppBundle/Repository/Contract/CertificateRequestRepositoryInterface.php`
4. Add return types and parameter type hints
5. Update repository to implement interface
6. Add binding in `services.yml`

**Methods to Include (verify in repository):**
- `findByUser(User $user): array`
- `findPendingRequests(): array`
- Any other public methods

---

### 2. InfoMeetingRepository
**Entity:** `AppBundle\Entity\InfoMeeting`  
**Repository:** `src/AppBundle/Entity/Repository/InfoMeetingRepository.php`  
**Priority:** Low (minor usage)

**Steps:**
1. Read existing repository file
2. Identify all public methods
3. Create interface at: `src/AppBundle/Repository/Contract/InfoMeetingRepositoryInterface.php`
4. Add return types and parameter type hints
5. Update repository to implement interface
6. Add binding in `services.yml`

**Methods to Include (verify in repository):**
- `findByDepartment(Department $department): array`
- `findUpcoming(): array`
- Any other public methods

---

### 3. PositionRepository
**Entity:** `AppBundle\Entity\Position`  
**Repository:** `src/AppBundle/Entity/Repository/PositionRepository.php`  
**Priority:** Low (check if needed)

**Steps:**
1. **First, verify if this repository is actually used:**
   ```bash
   grep -r "PositionRepository" src/
   ```
2. If used, read repository file and identify methods
3. Create interface at: `src/AppBundle/Repository/Contract/PositionRepositoryInterface.php`
4. Add return types and parameter type hints
5. Update repository to implement interface
6. Add binding in `services.yml`

**Methods to Include (verify in repository):**
- Common methods like `findAll(): array`, `find($id): ?Position`

---

### 4. SponsorRepository
**Entity:** `AppBundle\Entity\Sponsor`  
**Repository:** `src/AppBundle/Entity/Repository/SponsorRepository.php`  
**Priority:** Low (API only)

**Steps:**
1. Read existing repository file
2. Identify all public methods
3. Create interface at: `src/AppBundle/Repository/Contract/SponsorRepositoryInterface.php`
4. Add return types and parameter type hints
5. Update repository to implement interface
6. Add binding in `services.yml`

**Methods to Include (verify in repository):**
- `findActive(): array`
- `findByDepartment(Department $department): array`
- Any other public methods

**Note:** This is primarily used by API controllers

---

### 5. SurveyAnswerRepository
**Entity:** `AppBundle\Entity\SurveyAnswer`  
**Repository:** `src/AppBundle/Entity/Repository/SurveyAnswerRepository.php`  
**Priority:** Low (check if needed)

**Steps:**
1. **First, verify if this repository is actually used:**
   ```bash
   grep -r "SurveyAnswerRepository" src/
   ```
2. If used, read repository file and identify methods
3. Create interface at: `src/AppBundle/Repository/Contract/SurveyAnswerRepositoryInterface.php`
4. Add return types and parameter type hints
5. Update repository to implement interface
6. Add binding in `services.yml`

**Methods to Include (verify in repository):**
- `findBySurveyTaken(SurveyTaken $surveyTaken): array`
- `findByQuestion(SurveyQuestion $question): array`
- Any other public methods

---

### 6. UserGroupRepository / UserGroupCollectionRepository
**Entity:** `AppBundle\Entity\UserGroup` / `AppBundle\Entity\UserGroupCollection`  
**Repository:** `src/AppBundle/Entity/Repository/UserGroupRepository.php` (and/or UserGroupCollectionRepository)  
**Priority:** Low (minor usage)

**Steps:**
1. **First, identify which repositories exist:**
   ```bash
   ls src/AppBundle/Entity/Repository/*UserGroup*.php
   ```
2. For each repository found:
   - Read repository file
   - Identify all public methods
   - Create corresponding interface
   - Update repository to implement interface
   - Add binding in `services.yml`

**Possible Methods (verify in repositories):**
- `findByCollection(UserGroupCollection $collection): array`
- `findActive(): array`
- Any other public methods

---

## Implementation Pattern

### Step 1: Analyze Repository
```bash
# Read the repository file
cat src/AppBundle/Entity/Repository/[RepositoryName]Repository.php
```

### Step 2: Extract Method Signatures
- Identify all `public function` methods
- Note return types (check `@return` PHPDoc)
- Note parameter types
- Distinguish between `array`, `QueryBuilder`, entity, or `null` returns

### Step 3: Create Interface
Follow this pattern:

```php
<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\CertificateRequest;
use AppBundle\Entity\User;

/**
 * Interface for CertificateRequest repository operations.
 * This interface makes it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface CertificateRequestRepositoryInterface
{
    /**
     * Find all certificate requests for a user.
     *
     * @param User $user
     * @return CertificateRequest[]
     */
    public function findByUser(User $user): array;

    /**
     * Find all pending certificate requests.
     *
     * @return CertificateRequest[]
     */
    public function findPendingRequests(): array;
}
```

### Step 4: Update Repository Implementation
```php
<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\CertificateRequest;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\CertificateRequestRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class CertificateRequestRepository extends EntityRepository implements CertificateRequestRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): array
    {
        // Existing implementation
    }

    /**
     * {@inheritdoc}
     */
    public function findPendingRequests(): array
    {
        // Existing implementation
    }
}
```

### Step 5: Add Service Binding
Update `app/config/services.yml`:

```yaml
AppBundle\Repository\Contract\CertificateRequestRepositoryInterface:
    alias: AppBundle\Entity\Repository\CertificateRequestRepository
```

## Acceptance Criteria

- [ ] Interface file created with proper namespace
- [ ] All public repository methods included in interface
- [ ] Return types added (use `array` for collections, entity for single, `?Entity` for nullable)
- [ ] Parameter type hints added
- [ ] PHPDoc comments complete with `@param` and `@return`
- [ ] Repository updated to implement interface
- [ ] `{@inheritdoc}` tags added to repository methods
- [ ] Interface bound in `services.yml`
- [ ] Syntax validated: `php -l [file]`
- [ ] No linter errors

## Notes

- These are **low-priority** repositories with minor usage
- Some may not be used at all (Position, SurveyAnswer) - verify first
- Follow exact same pattern as existing repository interfaces
- See `src/AppBundle/Repository/Contract/` for examples
- Maintain consistency with existing interface structure

## Verification

After completing all interfaces:

1. **Check completeness:**
   ```bash
   # Count interfaces
   ls src/AppBundle/Repository/Contract/*.php | wc -l
   
   # Should be 38 total
   ```

2. **Check implementations:**
   ```bash
   # Verify all repositories implement their interfaces
   grep -r "implements.*RepositoryInterface" src/AppBundle/Entity/Repository/ | wc -l
   ```

3. **Check bindings:**
   ```bash
   # Verify all interfaces bound in services.yml
   grep "RepositoryInterface:" app/config/services.yml | wc -l
   ```


