# Agent Task: Create Service Interfaces

## Objective
Create interfaces for all service classes to enable dependency injection and prepare for Laravel migration.

## Current Status
- ✅ `MailerInterface` - Already exists
- ✅ `SmsSenderInterface` - Already exists
- ⏳ Remaining 30 services need interfaces

## Task List

### High Priority Services (Used by Controllers)
1. **ApplicationManagerInterface**
   - Service: `src/AppBundle/Service/ApplicationManager.php`
   - Methods: `getApplicationStatus()`, other application management methods

2. **InterviewManagerInterface**
   - Service: `src/AppBundle/Service/InterviewManager.php`
   - Methods: `initializeInterviewAnswers()`, `loggedInUserCanSeeInterview()`, etc.

3. **SurveyManagerInterface**
   - Service: `src/AppBundle/Service/SurveyManager.php`
   - Methods: `initializeSurveyTaken()`, `predictSurveyTakenAnswers()`, etc.

4. **UserServiceInterface**
   - Service: `src/AppBundle/Service/UserService.php`
   - Methods: User management operations

5. **RoleManagerInterface**
   - Service: `src/AppBundle/Service/RoleManager.php`
   - Methods: `isValidRole()`, `canChangeToRole()`, etc.

6. **FileUploaderInterface**
   - Service: `src/AppBundle/Service/FileUploader.php`
   - Methods: File upload operations for different types

7. **GeoLocationInterface**
   - Service: `src/AppBundle/Service/GeoLocation.php`
   - Methods: `sortDepartmentsByDistanceFromClient()`, `findNearestDepartment()`, etc.

### Medium Priority Services
8. **PasswordManagerInterface**
9. **LoginManagerInterface**
10. **UserRegistrationInterface**
11. **AdmissionNotifierInterface**
12. **SlackMessengerInterface**
13. **EmailSenderInterface**
14. **TeamMembershipServiceInterface**
15. **SurveyNotifierInterface**

### Lower Priority Services
16. **AccessControlServiceInterface**
17. **ApplicationAdmissionInterface**
18. **ApplicationDataInterface**
19. **AssistantHistoryDataInterface**
20. **CompanyEmailMakerInterface**
21. **ContentModeManagerInterface**
22. **FilterServiceInterface**
23. **InterviewCounterInterface**
24. **InterviewNotificationManagerInterface**
25. **LogServiceInterface**
26. **SbsDataInterface**
27. **SlackMailerInterface**
28. **SlugMakerInterface**
29. **SorterInterface**
30. **AdmissionStatisticsInterface**
31. **BetaRedirecterInterface**
32. **UserGroupCollectionManagerInterface**

## Implementation Pattern

### Step 1: Read Service File
```bash
# Read the service file
# Example: src/AppBundle/Service/ApplicationManager.php
```

### Step 2: Extract Public Methods
- List all public methods
- Note parameters and types
- Note return types
- Identify dependencies (constructor parameters)

### Step 3: Create Interface File
Location: `src/AppBundle/Service/Contract/{ServiceName}Interface.php`

```php
<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Application;

/**
 * Interface for ApplicationManager service.
 * Defines contract for application management operations.
 */
interface ApplicationManagerInterface
{
    /**
     * Get application status for the given application.
     *
     * @param Application $application
     * @return string|array Status information
     */
    public function getApplicationStatus(Application $application);

    // Add all other public methods...
}
```

### Step 4: Update Service Implementation
```php
<?php

namespace AppBundle\Service;

use AppBundle\Service\Contract\ApplicationManagerInterface;

class ApplicationManager implements ApplicationManagerInterface
{
    // Existing implementation remains unchanged
}
```

### Step 5: Update Service Configuration
Update `app/config/services.yml`:

```yaml
services:
    AppBundle\Service\Contract\ApplicationManagerInterface:
        alias: AppBundle\Service\ApplicationManager
```

Or use bind (preferred):
```yaml
services:
    _defaults:
        bind:
            AppBundle\Service\Contract\ApplicationManagerInterface: '@AppBundle\Service\ApplicationManager'
```

## Special Cases

### Services That Extend Others
If service extends another service, interface should extend parent interface:
```php
interface ChildServiceInterface extends ParentServiceInterface
{
    // Child-specific methods
}
```

### Services with Complex Dependencies
Include constructor parameter types in documentation, not in interface (interfaces don't have constructors).

### Services Returning Framework Objects
If service returns Symfony-specific objects (Response, RedirectResponse), consider:
1. Keep for now (interface will change in Laravel migration)
2. Document in PHPDoc that return type will change

## Acceptance Criteria

- [ ] Interface file created in `src/AppBundle/Service/Contract/`
- [ ] All public methods from service are in interface
- [ ] Proper type hints and return types
- [ ] PHPDoc comments for methods with parameter descriptions
- [ ] Service class implements the interface
- [ ] Service configuration updated
- [ ] No syntax errors

## Testing

After creating interface:
1. Verify syntax: `php bin/console lint:container`
2. Verify autowiring works
3. Test using interface in a controller or other service

## Example Reference

See existing interfaces:
- `src/AppBundle/Mailer/MailerInterface.php` (if exists)
- `src/AppBundle/Sms/SmsSenderInterface.php` (if exists)

## Notes

- Method signatures should match exactly
- Return types should be preserved
- If return type is ambiguous (mixed), use `@return` annotation
- Document complex parameter types
- Keep interface methods public (they are public in service)

## Progress Tracking

After completing each interface:
1. Mark it as complete in this file
2. Commit: "feat: Add {ServiceName}Interface"
3. Update service configuration
4. Move to next service

