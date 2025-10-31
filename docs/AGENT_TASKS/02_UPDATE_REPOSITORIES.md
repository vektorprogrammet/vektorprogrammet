# Agent Task: Update Existing Repositories to Implement Interfaces

## Objective
Update all existing Doctrine repositories to implement their corresponding interfaces.

## Current Status
- ⏳ No repositories implement interfaces yet
- ✅ Interfaces created (or being created by Agent 01)

## Task List

For each repository interface created:

### Example: ArticleRepository

**Before:**
```php
<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    // methods...
}
```

**After:**
```php
<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    // methods remain unchanged
}
```

## Implementation Steps

1. **Open Repository File**
   - Location: `src/AppBundle/Entity/Repository/{EntityName}Repository.php`

2. **Add Interface Import**
   ```php
   use AppBundle\Repository\Contract\{EntityName}RepositoryInterface;
   ```

3. **Update Class Declaration**
   ```php
   class {EntityName}Repository extends EntityRepository implements {EntityName}RepositoryInterface
   ```

4. **Verify No Changes Needed**
   - Methods should already match interface
   - If not, update method signatures to match interface

## Priority Order

Update repositories in the same order as interfaces are created:

1. ArticleRepository (when ArticleRepositoryInterface exists)
2. UserRepository (when UserRepositoryInterface exists)
3. DepartmentRepository
4. AdmissionPeriodRepository
5. ApplicationRepository
6. SemesterRepository
7. AssistantHistoryRepository
8. InterviewRepository
9. SurveyRepository
10. ReceiptRepository
11. TeamRepository
12. SchoolRepository

## Acceptance Criteria

- [ ] Repository class implements corresponding interface
- [ ] All interface methods exist in repository (or will be added)
- [ ] Method signatures match interface exactly
- [ ] No syntax errors
- [ ] Existing functionality still works

## Testing

After updating each repository:

1. **Syntax Check:**
   ```bash
   php bin/console lint:container
   ```

2. **Verify Interface Binding:**
   Check that service can be autowired by interface

3. **Functional Test:**
   Use repository in existing controller action to ensure nothing broke

## Common Issues

### Issue 1: Method Signature Mismatch
**Problem:** Repository method has different signature than interface

**Solution:** Update interface to match repository (preserve existing behavior), OR update repository to match interface (if safe to change)

### Issue 2: Missing Methods
**Problem:** Interface declares method that doesn't exist in repository

**Solution:** Add method to repository OR remove from interface (verify it's not used)

### Issue 3: Return Type Mismatch
**Problem:** Interface says returns `array` but repository returns `QueryBuilder`

**Solution:** Check interface definition - should match actual usage

## Notes

- **Do NOT change existing method implementations**
- Only add `implements` clause
- Method bodies remain unchanged
- If methods need updating, create separate task

## Progress Tracking

After updating each repository:
1. Mark complete in this file
2. Test the change
3. Commit: "feat: Make {EntityName}Repository implement interface"
4. Move to next repository

