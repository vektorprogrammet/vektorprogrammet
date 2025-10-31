# Agent Task: Create Repository Interfaces

## ⚠️ Important: Read Coding Standards First
**Before starting this task, read:**
- `docs/AGENT_TASKS/CODING_STANDARDS.md`

This task requires:
- ✅ Type hints on all parameters
- ✅ Return types on all methods
- ✅ Complete PHPDoc comments

## Objective
Create repository interfaces for all major entities to enable dependency injection and prepare for Laravel migration.

## Current Status
- ✅ `ArticleRepositoryInterface` - Created
- ✅ `UserRepositoryInterface` - Created
- ⏳ Remaining repositories need interfaces

## Task List

### High Priority (Complete First)
1. **DepartmentRepositoryInterface**
   - Location: `src/AppBundle/Repository/Contract/DepartmentRepositoryInterface.php`
   - Based on: `src/AppBundle/Entity/Repository/DepartmentRepository.php`
   - Methods to include: `findAll()`, `findAllDepartments()`, `findAllWithActiveAdmission()`, `find($id)`

2. **AdmissionPeriodRepositoryInterface**
   - Location: `src/AppBundle/Repository/Contract/AdmissionPeriodRepositoryInterface.php`
   - Based on: `src/AppBundle/Entity/Repository/AdmissionPeriodRepository.php`
   - Methods: `findOneByDepartmentAndSemester()`, `findCurrentForDepartment()`

3. **ApplicationRepositoryInterface**
   - Location: `src/AppBundle/Repository/Contract/ApplicationRepositoryInterface.php`
   - Based on: `src/AppBundle/Entity/Repository/ApplicationRepository.php`
   - Methods: `findByUserInAdmissionPeriod()`, `findActiveApplications()`

4. **SemesterRepositoryInterface**
   - Location: `src/AppBundle/Repository/Contract/SemesterRepositoryInterface.php`
   - Based on: `src/AppBundle/Entity/Repository/SemesterRepository.php`
   - Methods: `findOrCreateCurrentSemester()`, `findAll()`, `find($id)`

5. **AssistantHistoryRepositoryInterface**
   - Location: `src/AppBundle/Repository/Contract/AssistantHistoryRepositoryInterface.php`
   - Based on: `src/AppBundle/Entity/Repository/AssistantHistoryRepository.php`
   - Methods: `findActiveAssistantHistoriesByUser()`, `findActiveAssistantHistoriesBySchool()`, `numFemale()`, `numMale()`

### Medium Priority
6. **InterviewRepositoryInterface**
7. **SurveyRepositoryInterface**
8. **ReceiptRepositoryInterface**
9. **TeamRepositoryInterface**
10. **SchoolRepositoryInterface**

## Implementation Pattern

### Step 1: Read Existing Repository
```bash
# Read the existing repository file
# Example: src/AppBundle/Entity/Repository/DepartmentRepository.php
```

### Step 2: Extract Method Signatures
- Identify all public methods
- Note return types (use `@return` annotations if not in signature)
- Note parameter types
- Identify QueryBuilder returns vs arrays

### Step 3: Create Interface File
Follow this pattern:

```php
<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Department;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface for Department repository operations.
 * This interface defines the contract for department data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface DepartmentRepositoryInterface
{
    /**
     * Find all departments.
     *
     * @return Department[]
     */
    public function findAll(): array;

    /**
     * Find all departments (returns QueryBuilder for pagination).
     *
     * @return QueryBuilder
     */
    public function findAllDepartments(): QueryBuilder;

    /**
     * Find all departments with active admission periods.
     *
     * @return Department[]
     */
    public function findAllWithActiveAdmission(): array;

    /**
     * Find department by ID.
     *
     * @param int $id
     * @return Department|null
     */
    public function find(int $id): ?Department;
}
```

### Step 4: Update Existing Repository
Add `implements` clause:

```php
class DepartmentRepository extends EntityRepository implements DepartmentRepositoryInterface
{
    // Existing methods remain unchanged
}
```

### Step 5: Update Service Configuration
Add to `app/config/services.yml`:

```yaml
services:
    AppBundle\Repository\Contract\DepartmentRepositoryInterface:
        alias: AppBundle\Entity\Repository\DepartmentRepository
```

## Acceptance Criteria

- [ ] Interface file created in `src/AppBundle/Repository/Contract/`
- [ ] All public methods from repository are in interface
- [ ] Proper type hints and return types
- [ ] PHPDoc comments for methods
- [ ] Existing repository implements the interface
- [ ] Service configuration updated
- [ ] No syntax errors (run `php bin/console lint:container`)

## Testing

After creating interface:
1. Verify syntax: `php bin/console lint:container`
2. Check autowiring works
3. Try using interface in a test controller

## Example Reference

See completed examples:
- `src/AppBundle/Repository/Contract/ArticleRepositoryInterface.php`
- `src/AppBundle/Repository/Contract/UserRepositoryInterface.php`

## Notes

- QueryBuilder return types are kept for compatibility with KNP Paginator
- Use array return types where collections are returned
- Use nullable return types (`?Entity`) where `find()` might return null
- Preserve method names exactly as they are in the repository

## Files to Create

For each repository, create:
- `src/AppBundle/Repository/Contract/{EntityName}RepositoryInterface.php`
- Update `src/AppBundle/Entity/Repository/{EntityName}Repository.php` to implement interface
- Update `app/config/services.yml` to bind interface

## Progress Tracking

After completing each interface:
1. Mark it as complete in this file
2. Commit changes with message: "feat: Add {EntityName}RepositoryInterface"
3. Move to next repository in priority order

