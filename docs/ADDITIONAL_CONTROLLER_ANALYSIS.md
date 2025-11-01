# Additional Controller Analysis

This document contains detailed analysis of additional controllers that may need business logic extraction.

---

## 1. AssistantSchedulingController

### Basic Info
- **File:** `src/AppBundle/Controller/AssistantSchedulingController.php`
- **Lines of Code:** 153
- **Service Locator Calls:** 0 (good!)
- **Dependencies Injected:** 
  - `AdmissionPeriodRepositoryInterface`
  - `ApplicationRepositoryInterface`
  - `EntityManagerInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Assistant Data Transformation (`getAssistantAvailableDays`)
- **Lines:** 67-107
- **Logic:** Transforms `Application` entities into `Assistant` objects with availability data, scores, and preferences
- **Complexity:** Medium
- **Dependencies:** 
  - `Application[]` entities
  - Interview score calculation
  - Preferred group mapping logic
- **Extract to:** `transformApplicationsToAssistants()`

**Details:**
- Maps preferred group strings ('Bolk 1', 'Bolk 2') to integers
- Builds availability array from application day booleans
- Calculates suitability scores (20 for previous participation, otherwise interview score)
- Handles double position logic (nullifies preferred group)

#### Section 2: School Capacity Transformation (`generateSchoolsFromSchoolCapacities`)
- **Lines:** 130-151
- **Logic:** Transforms `SchoolCapacity` entities into `School` objects for scheduling algorithm
- **Complexity:** Low-Medium
- **Dependencies:** 
  - `SchoolCapacity[]` entities
  - School entity data
- **Extract to:** `transformSchoolCapacitiesToSchools()`

**Details:**
- Maps daily capacities from SchoolCapacity to School objects
- Creates capacity arrays for both groups (1 and 2)

### Recommended Services

#### AssistantSchedulingDataService
**Interface:** `AppBundle\Service\Contract\AssistantSchedulingDataServiceInterface`

**Methods:**
- `transformApplicationsToAssistants(array $applications): array` - Converts Application entities to Assistant objects
- `transformSchoolCapacitiesToSchools(array $schoolCapacities): array` - Converts SchoolCapacity entities to School objects
- `calculateAssistantScore(Application $application): int` - Calculates suitability score (consolidates score logic)

**Dependencies Needed:**
- None (pure transformation logic)

### Priority Assessment
- **Priority:** Medium
- **Reason:** Business logic for data transformation that is used specifically for scheduling algorithm. While not overly complex, extracting it improves testability and separation of concerns. The logic could be reused if scheduling algorithm changes.
- **Estimated Complexity:** Low
- **Estimated Time:** 1-2 hours

### Next Steps
- [ ] Create `AssistantSchedulingDataServiceInterface`
- [ ] Create `AssistantSchedulingDataService` implementation
- [ ] Extract transformation methods
- [ ] Update controller to use service
- [ ] Test functionality

---

## 2. ExistingUserAdmissionController

### Basic Info
- **File:** `src/AppBundle/Controller/ExistingUserAdmissionController.php`
- **Lines of Code:** 94
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `EntityManagerInterface`
  - `ApplicationAdmissionInterface` ✅ (already using service)
  - `TeamRepositoryInterface`
  - `EventDispatcherInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

**Note:** This controller is already well-delegated! The main business logic is handled by `ApplicationAdmissionInterface` service.

- Uses `ApplicationAdmissionInterface::renderErrorPage()` for validation
- Uses `ApplicationAdmissionInterface::createApplicationForExistingAssistant()` for application creation
- Simple form handling and event dispatching

### Recommended Services

**No additional extraction needed.** Controller already follows best practices with:
- Business logic delegated to `ApplicationAdmissionInterface`
- Repositories properly injected
- Event dispatching handled correctly

### Priority Assessment
- **Priority:** Low (already good)
- **Reason:** Controller is already well-structured with business logic properly delegated to services
- **Estimated Complexity:** N/A
- **Estimated Time:** N/A

### Next Steps
- ✅ Already properly structured

---

## 3. SubstituteController

### Basic Info
- **File:** `src/AppBundle/Controller/SubstituteController.php`
- **Lines of Code:** 131
- **Service Locator Calls:** 2 (lines 101, 120 - `getDoctrine()->getManager()`)
- **Dependencies Injected:**
  - `AdmissionPeriodRepositoryInterface`
  - `ApplicationRepositoryInterface`
  - `EntityManagerInterface` (but not used consistently)
- **Still Using Service Locator:** Yes (inconsistent EntityManager usage)

### Business Logic Sections

#### Section 1: Substitute Status Management
- **Lines:** 97-110, 112-129
- **Logic:** Managing substitute status (enable/disable) on applications
- **Complexity:** Low
- **Dependencies:**
  - Application entity
  - EntityManager
- **Extract to:** `setSubstituteStatus()`, `removeSubstituteStatus()`

**Issues:**
- Uses `getDoctrine()->getManager()` instead of injected `$this->entityManager` in two methods
- Simple status toggle logic

#### Section 2: Validation Logic
- **Lines:** 64-69, 114-117
- **Logic:** Validates that application is/isn't already a substitute
- **Complexity:** Low
- **Dependencies:** Application entity
- **Extract to:** Validation could be in service

### Recommended Services

#### SubstituteManagementService
**Interface:** `AppBundle\Service\Contract\SubstituteManagementServiceInterface`

**Methods:**
- `enableSubstituteStatus(Application $application): void` - Sets application as substitute
- `disableSubstituteStatus(Application $application): void` - Removes substitute status
- `canCreateSubstitute(Application $application): bool` - Validates if application can become substitute
- `canModifySubstitute(Application $application): bool` - Validates if substitute can be modified

**Dependencies Needed:**
- `ApplicationRepositoryInterface`
- `EntityManagerInterface`

### Priority Assessment
- **Priority:** Medium
- **Reason:** 
  - Still uses service locator pattern (getDoctrine())
  - Simple business logic that should be testable
  - Inconsistent EntityManager usage needs fixing
- **Estimated Complexity:** Low
- **Estimated Time:** 1-2 hours

### Next Steps
- [ ] Create `SubstituteManagementServiceInterface`
- [ ] Create `SubstituteManagementService` implementation
- [ ] Extract status management logic
- [ ] Replace service locator calls with injected EntityManager
- [ ] Update controller to use service
- [ ] Test functionality

---

## 4. TeamApplicationController

### Basic Info
- **File:** `src/AppBundle/Controller/TeamApplicationController.php`
- **Lines of Code:** 110
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `EntityManagerInterface`
  - `EventDispatcherInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Access Control Logic
- **Lines:** 38-41, 52-55
- **Logic:** Checks if user has access to view team applications (team membership or team leader role)
- **Complexity:** Low
- **Dependencies:**
  - `TeamMembership` repository
  - `Roles` (TEAM_LEADER)
- **Extract to:** `canViewTeamApplications()`

**Details:**
- Duplicated logic in two methods (`showApplicationAction`, `showAllApplicationsAction`)
- Access control should be in service layer for reusability

#### Section 2: Application Workflow
- **Lines:** 71-96
- **Logic:** Creates team application, validates deadline, dispatches event
- **Complexity:** Low-Medium
- **Dependencies:**
  - Team entity
  - Form validation
  - Event dispatcher
- **Extract to:** `submitTeamApplication()`

### Recommended Services

#### TeamApplicationService
**Interface:** `AppBundle\Service\Contract\TeamApplicationServiceInterface`

**Methods:**
- `canViewTeamApplication(User $user, TeamApplication $application): bool` - Access control check
- `canViewTeamApplications(User $user, Team $team): bool` - Access control check for all applications
- `submitTeamApplication(TeamApplication $application, Team $team): void` - Creates and persists team application
- `deleteTeamApplication(TeamApplication $teamApplication): void` - Removes team application

**Dependencies Needed:**
- `TeamMembershipRepositoryInterface` (or access via EntityManager)
- `RoleManagerInterface` (for role checks)
- `EntityManagerInterface`
- `EventDispatcherInterface`

### Priority Assessment
- **Priority:** Medium
- **Reason:** 
  - Access control logic is duplicated
  - Workflow logic should be testable
  - Follows pattern of other extracted services
- **Estimated Complexity:** Low
- **Estimated Time:** 2-3 hours

### Next Steps
- [ ] Create `TeamApplicationServiceInterface`
- [ ] Create `TeamApplicationService` implementation
- [ ] Extract access control logic
- [ ] Extract application workflow
- [ ] Update controller to use service
- [ ] Test functionality

---

## 5. WidgetController

### Basic Info
- **File:** `src/AppBundle/Controller/WidgetController.php`
- **Lines of Code:** 165
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - Multiple repositories
  - `SorterInterface`
  - `AdmissionStatisticsInterface`
  - `EntityManagerInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Receipt Statistics Aggregation (`receiptsAction`)
- **Lines:** 83-100
- **Logic:** Aggregates receipt data, sorts users, calculates statistics
- **Complexity:** Medium
- **Dependencies:**
  - `UserRepositoryInterface`
  - `ReceiptRepositoryInterface`
  - `SorterInterface`
  - `ReceiptStatistics` utility class
- **Extract to:** `getReceiptWidgetData()`

**Details:**
- Multiple sorting operations
- Statistics calculation
- Status filtering

#### Section 2: Interview Assignment Aggregation (`interviewsAction`)
- **Lines:** 69-81
- **Logic:** Finds applications assigned to user for interviews
- **Complexity:** Low
- **Dependencies:**
  - `ApplicationRepositoryInterface`
  - `AdmissionPeriodRepositoryInterface`
- **Already using repository properly** - minimal logic

#### Section 3: Application Graph Data (`applicationGraphAction`)
- **Lines:** 106-123
- **Logic:** Aggregates application data for graph visualization
- **Complexity:** Low
- **Dependencies:**
  - `ApplicationRepositoryInterface`
  - `AdmissionStatisticsInterface` (already delegated)
- **Already delegated to service** ✅

### Recommended Services

#### ReceiptWidgetService
**Interface:** `AppBundle\Service\Contract\ReceiptWidgetServiceInterface`

**Methods:**
- `getReceiptWidgetData(): array` - Returns sorted users, statistics, and pending receipts for widget display

**Dependencies Needed:**
- `UserRepositoryInterface`
- `ReceiptRepositoryInterface`
- `SorterInterface`

**Note:** Other widget actions already use appropriate services or are simple enough.

### Priority Assessment
- **Priority:** Low-Medium
- **Reason:** 
  - Receipt widget has aggregation logic that could be extracted
  - Other widgets are already simple or properly delegated
  - Not high priority but would improve testability
- **Estimated Complexity:** Low
- **Estimated Time:** 1-2 hours

### Next Steps
- [ ] Create `ReceiptWidgetServiceInterface`
- [ ] Create `ReceiptWidgetService` implementation
- [ ] Extract receipt aggregation logic
- [ ] Update controller to use service
- [ ] Test functionality

---

## 6. InterviewSchemaController

### Basic Info
- **File:** `src/AppBundle/Controller/InterviewSchemaController.php`
- **Lines of Code:** 114
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `EntityManagerInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Access Control with Business Logic
- **Lines:** 92-112
- **Logic:** Deletes interview schema with access control check
- **Complexity:** Low
- **Dependencies:**
  - `Roles::TEAM_LEADER`
  - EntityManager
- **Extract to:** `deleteInterviewSchema()`

**Details:**
- Access control check (TEAM_LEADER only)
- Exception handling
- Returns JSON response with success/error

### Recommended Services

#### InterviewSchemaService
**Interface:** `AppBundle\Service\Contract\InterviewSchemaServiceInterface`

**Methods:**
- `deleteInterviewSchema(InterviewSchema $schema, User $user): array` - Deletes schema with access control, returns result array
- `canDeleteSchema(InterviewSchema $schema, User $user): bool` - Access control check

**Dependencies Needed:**
- `EntityManagerInterface`
- `RoleManagerInterface` (or direct role checking)

### Priority Assessment
- **Priority:** Low
- **Reason:** 
  - Simple CRUD with minimal business logic
  - Access control could be extracted but not critical
  - Most actions are simple form handling
- **Estimated Complexity:** Low
- **Estimated Time:** 1 hour

### Next Steps
- [ ] (Optional) Create `InterviewSchemaServiceInterface` for delete operation
- [ ] Extract delete logic with access control
- [ ] Update controller to use service
- [ ] Test functionality

---

## 7. SurveyNotifierController

### Basic Info
- **File:** `src/AppBundle/Controller/SurveyNotifierController.php`
- **Lines of Code:** 138
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `EntityManagerInterface`
  - `SurveyNotifierInterface` ✅ (already using service)
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Email Preview Logic
- **Lines:** 59-82
- **Logic:** Renders email preview based on email type
- **Complexity:** Medium
- **Dependencies:**
  - SurveyNotificationCollection
  - Email template selection logic
  - Subject generation for type 2
- **Extract to:** `renderEmailPreview()`

**Details:**
- Maps email types to templates
- Generates subject for personal emails (type 2)
- Uses hardcoded school name ("Blussvoll")

### Recommended Services

#### SurveyNotificationPreviewService
**Interface:** `AppBundle\Service\Contract\SurveyNotificationPreviewServiceInterface`

**Methods:**
- `renderEmailPreview(SurveyNotificationCollection $collection, User $user, RouterInterface $router): array` - Returns preview data for email rendering

**Dependencies Needed:**
- `RouterInterface` (for URL generation)
- `SurveyNotifierInterface` (or just access to survey data)

**Note:** Or could extend `SurveyNotifierInterface` with preview method.

### Priority Assessment
- **Priority:** Low
- **Reason:** 
  - Controller already delegates main business logic to `SurveyNotifierInterface`
  - Preview logic is presentation-related and could stay in controller
  - Hardcoded school name is a minor issue
- **Estimated Complexity:** Low
- **Estimated Time:** 1 hour (if extracting preview)

### Next Steps
- [ ] (Optional) Extract email preview logic to service if reuse is needed
- [ ] Consider extracting hardcoded school name to configuration

---

## 8. SurveyPopupController

### Basic Info
- **File:** `src/AppBundle/Controller/SurveyPopupController.php`
- **Lines of Code:** 69
- **Service Locator Calls:** 0 (uses `RequestStack` properly)
- **Dependencies Injected:**
  - `RoleManagerInterface`
  - `EntityManagerInterface`
  - `SurveyRepositoryInterface`
  - `RequestStack`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Popup Display Logic
- **Lines:** 38-67
- **Logic:** Determines if user should see survey popup based on multiple conditions
- **Complexity:** Medium
- **Dependencies:**
  - User entity
  - Role checks
  - Time-based logic (1 day check)
  - Survey repository
- **Extract to:** `shouldShowSurveyPopup()`, `getNextSurvey()`

**Details:**
- Multiple conditions: user exists, is team member, not reserved from popup, 1+ days since last popup
- Date calculation logic
- Survey selection logic

### Recommended Services

#### SurveyPopupService
**Interface:** `AppBundle\Service\Contract\SurveyPopupServiceInterface`

**Methods:**
- `shouldShowSurveyPopup(User $user): bool` - Determines if popup should be shown
- `getNextSurveyForUser(User $user, Semester $semester): ?Survey` - Gets next available survey

**Dependencies Needed:**
- `SurveyRepositoryInterface`
- `RoleManagerInterface`

### Priority Assessment
- **Priority:** Medium
- **Reason:** 
  - Business logic for popup display rules
  - Date calculation logic
  - Multiple conditions that should be testable
- **Estimated Complexity:** Low-Medium
- **Estimated Time:** 2 hours

### Next Steps
- [ ] Create `SurveyPopupServiceInterface`
- [ ] Create `SurveyPopupService` implementation
- [ ] Extract popup display logic
- [ ] Extract survey selection logic
- [ ] Update controller to use service
- [ ] Test functionality

---

## 9. ParticipantHistoryController

### Basic Info
- **File:** `src/AppBundle/Controller/ParticipantHistoryController.php`
- **Lines of Code:** 56
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `AssistantHistoryRepositoryInterface`
  - `EntityManagerInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

**Note:** This controller is primarily a data retrieval controller. The logic is minimal:

- Access control check (line 38-40)
- Repository queries

### Recommended Services

**No extraction needed.** Controller is simple enough:
- Uses repositories properly
- Access control is straightforward
- No complex business logic

### Priority Assessment
- **Priority:** Low
- **Reason:** Simple data retrieval with minimal business logic. Already follows best practices.
- **Estimated Complexity:** N/A
- **Estimated Time:** N/A

### Next Steps
- ✅ Already properly structured

---

## 10. MailingListController

### Basic Info
- **File:** `src/AppBundle/Controller/MailingListController.php`
- **Lines of Code:** 112
- **Service Locator Calls:** 0
- **Dependencies Injected:**
  - `UserRepositoryInterface`
- **Still Using Service Locator:** No

### Business Logic Sections

#### Section 1: Routing Logic Based on Type
- **Lines:** 39-57
- **Logic:** Routes to different mailing list generation based on type selection
- **Complexity:** Low
- **Dependencies:** Form data
- **Extract to:** Could be in service for cleaner separation

#### Section 2: User Aggregation (`showAllAction`)
- **Lines:** 99-110
- **Logic:** Merges assistant and team users, removes duplicates
- **Complexity:** Low
- **Dependencies:**
  - UserRepository methods
- **Extract to:** `aggregateAllMailingListUsers()`

**Details:**
- Calls two repository methods
- Merges arrays and removes duplicates

### Recommended Services

#### MailingListService
**Interface:** `AppBundle\Service\Contract\MailingListServiceInterface`

**Methods:**
- `getAssistantMailingList(Department $department, Semester $semester): array` - Gets assistant users
- `getTeamMailingList(Department $department, Semester $semester): array` - Gets team users
- `getAllMailingList(Department $department, Semester $semester): array` - Gets combined unique users
- `resolveMailingListRoute(string $type, Department $department, Semester $semester): string` - Returns route name for type

**Dependencies Needed:**
- `UserRepositoryInterface`

### Priority Assessment
- **Priority:** Low
- **Reason:** 
  - Simple data aggregation
  - Repository methods are already clean
  - Routing logic is minimal
- **Estimated Complexity:** Low
- **Estimated Time:** 1-2 hours

### Next Steps
- [ ] (Optional) Create `MailingListServiceInterface` if we want cleaner separation
- [ ] Extract user aggregation logic
- [ ] Update controller to use service
- [ ] Test functionality

---

## Summary by Priority

### High Priority
*None identified*

### Medium Priority
1. **AssistantSchedulingController** - Data transformation logic
2. **SubstituteController** - Status management + service locator removal
3. **TeamApplicationController** - Access control + workflow logic
4. **SurveyPopupController** - Popup display rules

### Low Priority
5. **WidgetController** - Receipt aggregation (optional)
6. **InterviewSchemaController** - Delete access control (optional)
7. **MailingListController** - User aggregation (optional)
8. **SurveyNotifierController** - Preview logic (optional)

### Already Well-Structured
- **ExistingUserAdmissionController** ✅
- **ParticipantHistoryController** ✅

---

## Recommendations

### Immediate Actions
1. Fix **SubstituteController** - Remove service locator usage (`getDoctrine()`)
2. Extract **AssistantSchedulingController** transformation logic - Improves testability

### Medium-term Actions
3. Extract **TeamApplicationController** access control - Removes duplication
4. Extract **SurveyPopupController** popup logic - Business rules should be testable

### Optional Actions
5. Consider extracting widget data aggregation if reused elsewhere
6. Extract mailing list aggregation if business rules become more complex

---

## Metrics

- **Total Controllers Analyzed:** 10
- **Controllers Using Service Locator:** 1 (SubstituteController)
- **Controllers Needing Extraction:** 4-5 (depending on priorities)
- **Controllers Already Well-Structured:** 2
- **Estimated Total Extraction Time:** 8-12 hours

