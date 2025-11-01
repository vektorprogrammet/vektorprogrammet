# Business Logic Extraction Analysis

**Generated:** Current Session  
**Purpose:** Identify controllers requiring business logic extraction to services

---

## Executive Summary

After analyzing 62 controllers in the codebase, **28 controllers** have significant business logic that should be extracted to service classes. The analysis is based on:
- Complexity of business operations
- Direct Doctrine/Repository access patterns
- Service locator usage (`$this->get()`)
- Multi-step workflows and calculations
- Data aggregation and transformation logic

**Current Status:** 3 services extracted (HomeService, PartnerService, ArticleService)  
**Recommended Next Steps:** Extract business logic from 28 additional controllers

---

## Priority Classification

### 🔴 HIGH PRIORITY - Complex Controllers (9 controllers)

These controllers contain substantial business logic, complex workflows, or critical operations that should be extracted immediately.

#### 1. InterviewController ⚠️ **CRITICAL**
**File:** `src/AppBundle/Controller/InterviewController.php`  
**Lines:** 610  
**Service Locator Calls:** 37

**Business Logic to Extract:**
- **Interview scheduling logic** (lines 211-275)
  - Map link validation (`validateLink()` method)
  - Response code generation
  - Status management
  - Default schedule form data preparation
- **Interview assignment workflow** (lines 304-392)
  - Bulk assignment logic
  - Interviewer assignment
  - Schema assignment
- **Interview state transitions** (lines 112-120, 399-418, 426-457)
  - Accept/cancel workflows
  - Status updates
  - Co-interviewer management
- **Access control checks** (lines 66-68, 136-140, 217-219)
  - Complex permission validation (uses InterviewManager but has additional logic)

**Recommended Service:** `InterviewSchedulingService`
**Extract Methods:**
- `scheduleInterview()` - Handle scheduling workflow
- `assignInterview()` - Assignment logic
- `validateMapLink()` - Link validation
- `manageInterviewState()` - State transitions
- `canUserAccessInterview()` - Access control (complement InterviewManager)

**Dependencies Already Injected:** InterviewManager, ApplicationManager (partial)  
**Still Using Service Locator:** Yes (37 calls)

---

#### 2. SurveyController ⚠️ **HIGH COMPLEXITY**
**File:** `src/AppBundle/Controller/SurveyController.php`  
**Lines:** 504  
**Service Locator Calls:** 28

**Business Logic to Extract:**
- **Survey execution workflow** (lines 42-82, 132-199)
  - Survey initialization based on target audience
  - User identification and validation
  - Assistant history lookup for assistant surveys
  - Survey taken management (delete old, create new)
- **Survey results processing** (lines 409-454)
  - Result aggregation
  - CSV generation logic
  - Text answer processing
- **Survey access control** (lines 487-503)
  - Complex permission checking
  - Confidential survey handling
  - Department-based access
- **Survey listing with statistics** (lines 312-353)
  - Total answered calculations
  - Global vs department surveys filtering

**Recommended Service:** `SurveyExecutionService`
**Extract Methods:**
- `executeSurvey()` - Main execution workflow
- `processSurveySubmission()` - Handle form submission
- `calculateSurveyStatistics()` - Statistics calculation
- `checkSurveyAccess()` - Access control
- `prepareSurveyResults()` - Result processing

**Dependencies Already Injected:** SurveyManager (partial)  
**Still Using Service Locator:** Yes (28 calls)

---

#### 3. AssistantController ⚠️ **ADMISSION WORKFLOW**
**File:** `src/AppBundle/Controller/AssistantController.php`  
**Lines:** 224  
**Service Locator Calls:** 13

**Business Logic to Extract:**
- **Admission page data preparation** (lines 90-160)
  - Department filtering and sorting by distance
  - Active admission filtering
  - Team filtering for open applications
  - Multiple form handling (one per department)
- **Application submission workflow** (lines 125-146, 200-216)
  - User identification/correction
  - Existing user detection
  - Admission period validation
  - Form submission across multiple departments
- **Department routing logic** (lines 41-66)
  - Case-insensitive city lookup
  - Short name handling

**Recommended Service:** `AdmissionService`
**Extract Methods:**
- `prepareAdmissionPageData()` - Data aggregation for admission page
- `submitApplication()` - Application submission workflow
- `findDepartmentByLocation()` - Department lookup logic
- `getTeamsForDepartment()` - Team filtering

**Dependencies Already Injected:** ApplicationAdmission, FilterService, GeoLocation (partial)  
**Still Using Service Locator:** Yes (13 calls)

---

#### 4. AdmissionAdminController ⚠️ **ADMIN WORKFLOWS**
**File:** `src/AppBundle/Controller/AdmissionAdminController.php`  
**Lines:** 340  
**Service Locator Calls:** 22

**Business Logic to Extract:**
- **Application filtering and categorization** (lines 45-173)
  - New/existing/assigned/interviewed application queries
  - Interview distribution calculations
  - Cancelled application tracking
  - Application status aggregation
- **Team interest matching** (lines 307-339)
  - Team interest to application matching
  - Team filtering by interest
  - Applicant filtering
- **Bulk operations** (lines 224-248)
  - Bulk deletion logic
  - Validation and processing

**Recommended Service:** `AdmissionManagementService`
**Extract Methods:**
- `getApplicationsByStatus()` - Status-based filtering
- `calculateInterviewDistributions()` - Distribution logic
- `matchTeamInterests()` - Interest matching
- `processBulkOperations()` - Bulk processing

**Dependencies Already Injected:** InterviewCounter (partial)  
**Still Using Service Locator:** Yes (22 calls)

---

#### 5. ProfileController ⚠️ **PROFILE OPERATIONS**
**File:** `src/AppBundle/Controller/ProfileController.php`  
**Lines:** 315  
**Service Locator Calls:** 24

**Business Logic to Extract:**
- **Certificate generation** (lines 175-217)
  - PDF generation with Dompdf
  - HTML rendering with template preparation
  - Data aggregation (assistant history, team memberships, signatures)
- **Profile data aggregation** (lines 30-86)
  - Multiple repository queries
  - History compilation (assistant, team, executive board)
  - Permission checking for profile visibility
- **User activation workflow** (lines 108-143)
  - Activation code validation
  - Token generation and session management

**Recommended Service:** `ProfileService`
**Extract Methods:**
- `generateCertificate()` - Certificate PDF generation
- `aggregateProfileData()` - Profile data compilation
- `activateUserByCode()` - Activation workflow
- `prepareCertificateData()` - Certificate data preparation

**Dependencies Already Injected:** UserRegistration, RoleManager, LogService (partial)  
**Still Using Service Locator:** Yes (24 calls)

---

#### 6. TeamAdminController ⚠️ **TEAM MANAGEMENT**
**File:** `src/AppBundle/Controller/TeamAdminController.php`  
**Lines:** 272  
**Service Locator Calls:** 21

**Business Logic to Extract:**
- **Team membership management** (lines 48-112, 247-256)
  - Active/inactive filtering
  - Membership sorting logic (`sortTeamMembershipsByEndDate`)
  - User-team relationship checking
  - Team membership creation workflow
- **Team deletion workflow** (lines 258-271)
  - Soft delete handling
  - Membership cleanup
  - Name preservation
- **Team data aggregation** (lines 30-46, 114-138)
  - Active/inactive team separation
  - Team membership compilation

**Recommended Service:** `TeamManagementService`
**Extract Methods:**
- `manageTeamMembership()` - Membership operations
- `sortTeamMemberships()` - Sorting logic
- `checkUserTeamMembership()` - Relationship checking
- `deleteTeam()` - Team deletion workflow
- `aggregateTeamData()` - Data compilation

**Dependencies Already Injected:** None  
**Still Using Service Locator:** Yes (21 calls)

---

#### 7. ReceiptController ⚠️ **STATISTICS & WORKFLOWS**
**File:** `src/AppBundle/Controller/ReceiptController.php`  
**Lines:** 280  
**Status:** ✅ Already has dependency injection

**Business Logic Still to Extract:**
- **Receipt statistics calculation** (lines 60-86)
  - Total payout calculations
  - Average refund time
  - Statistics aggregation from multiple sources
- **Receipt status workflow** (lines 188-217)
  - Status transition logic
  - Refund date management
  - Event dispatching coordination

**Recommended Service:** `ReceiptStatisticsService` or extend existing `ReceiptStatistics`
**Extract Methods:**
- `calculateReceiptStatistics()` - Statistics aggregation
- `processStatusChange()` - Status workflow
- `aggregateReceiptData()` - Data compilation

**Note:** This controller is already well-refactored with DI, but still has business logic in methods.

---

#### 8. UserAdminController ⚠️ **USER OPERATIONS**
**File:** `src/AppBundle/Controller/UserAdminController.php`  
**Lines:** 111  
**Service Locator Calls:** 11

**Business Logic to Extract:**
- **User creation workflow** (lines 15-51)
  - Default role assignment
  - User persistence
  - Activation code sending coordination
- **User data filtering** (lines 53-87)
  - Active/inactive user separation
  - Department-based filtering

**Recommended Service:** `UserManagementService` (or extend UserRegistration)
**Extract Methods:**
- `createUserWithDefaults()` - User creation workflow
- `filterUsersByStatus()` - User filtering

**Dependencies Already Injected:** UserRegistration (partial)  
**Still Using Service Locator:** Yes (11 calls)

---

#### 9. FeedbackController ⚠️ **ALREADY REFACTORED**
**File:** `src/AppBundle/Controller/FeedbackController.php`  
**Lines:** 108  
**Status:** ✅ Already has dependency injection

**Business Logic Still to Extract:**
- **Feedback submission workflow** (lines 52-65)
  - Feedback creation
  - Slack notification coordination
  - User association

**Recommended Service:** `FeedbackSubmissionService`
**Extract Methods:**
- `submitFeedback()` - Submission workflow

**Note:** Controller is already well-refactored, but workflow logic could be extracted.

---

### 🟡 MEDIUM PRIORITY - Moderate Complexity (12 controllers)

These controllers have moderate business logic that should be extracted but are less critical than high-priority ones.

#### 10. CertificateController
**Business Logic:**
- Certificate generation logic
- Signature retrieval
- Template preparation

#### 11. ApplicationStatisticsController
**Status:** ✅ Already refactored with DI
**Business Logic:** Minimal - mainly data aggregation, already using services

#### 12. StandController
**Status:** ✅ Already refactored
**Business Logic:** Check for additional extraction opportunities

#### 13. ContactController
**Status:** ✅ Already refactored
**Business Logic:** Check for additional extraction opportunities

#### 14. BoardAndTeamController
**Status:** ✅ Already refactored
**Business Logic:** Check for additional extraction opportunities

#### 15. SchoolAdminController
**Business Logic:**
- School capacity management
- School data aggregation
- Capacity calculations

#### 16. ArticleAdminController
**Business Logic:**
- Article moderation workflows
- Article status management
- Publishing workflows

#### 17. DepartmentController
**Business Logic:**
- Department data aggregation
- Department filtering logic

#### 18. SemesterController
**Business Logic:**
- Semester management
- Date range calculations

#### 19. FieldOfStudyController
**Business Logic:**
- Field of study filtering
- Study program management

#### 20. ExecutiveBoardController
**Business Logic:**
- Board membership management
- Board data aggregation

#### 21. AdmissionPeriodController
**Business Logic:**
- Admission period calculations
- Period validation logic

---

### 🟢 LOW PRIORITY - Simple CRUD (7 controllers) ✅ REVIEWED

**Status:** ✅ Review Complete - No extraction needed  
**Review Document:** `docs/AGENT_TASKS/09_LOW_PRIORITY_REVIEW_RESULTS.md`

These controllers are primarily CRUD operations with minimal business logic. After thorough review, all 7 controllers were determined to be pure CRUD operations with no extractable business logic.

#### 22. ChangeLogController
**Business Logic:** None - pure CRUD  
**Decision:** ✅ No extraction needed - simple logging operations

#### 23. StaticContentController
**Business Logic:** None - simple find-or-create pattern  
**Decision:** ✅ No extraction needed - access control already abstracted

#### 24. PositionController
**Business Logic:** None - pure CRUD  
**Decision:** ✅ No extraction needed - standard entity management

#### 25. SignatureController
**Business Logic:** Minimal - access control and file path extraction  
**Decision:** ✅ No extraction needed - logic is straightforward

#### 26. SocialEventController
**Business Logic:** None - pure CRUD with repository filtering  
**Decision:** ✅ No extraction needed - filtering handled at repository level

#### 27. SponsorsController
**Business Logic:** None - file handling already abstracted to service  
**Decision:** ✅ No extraction needed - uses FileUploaderInterface appropriately

#### 28. AccessRuleController
**Business Logic:** Already extracted - uses AccessControlService  
**Decision:** ✅ No extraction needed - already using services appropriately

---

## Detailed Extraction Recommendations

### InterviewController - Detailed Analysis

**Priority:** 🔴 CRITICAL

**Current Issues:**
- 610 lines of code
- 37 service locator calls
- Complex state management
- Multiple responsibilities

**Extract to InterviewSchedulingService:**
```php
interface InterviewSchedulingServiceInterface
{
    public function scheduleInterview(Interview $interview, array $formData): void;
    public function assignInterviewer(User $interviewer, Application $application, InterviewSchema $schema): void;
    public function bulkAssignInterviews(User $interviewer, array $applications, InterviewSchema $schema): void;
    public function validateMapLink(string $link): bool;
    public function getDefaultScheduleData(Interview $interview): array;
    public function manageInterviewResponse(Interview $interview, string $action): void;
    public function canUserAccessInterview(User $user, Interview $interview): bool;
}
```

**Methods to Extract:**
1. `scheduleAction()` logic (lines 211-275)
2. `assignAction()` logic (lines 304-338)
3. `bulkAssignAction()` logic (lines 352-392)
4. `validateLink()` private method (lines 277-291)
5. `requestNewTimeAction()` workflow (lines 426-457)
6. `cancelByResponseCodeAction()` workflow (lines 480-511)

---

### SurveyController - Detailed Analysis

**Priority:** 🔴 HIGH COMPLEXITY

**Current Issues:**
- 504 lines of code
- 28 service locator calls
- Complex workflow logic
- Multiple survey types handling

**Extract to SurveyExecutionService:**
```php
interface SurveyExecutionServiceInterface
{
    public function executeSurvey(Survey $survey, User $user = null, string $identifier = null): array;
    public function processSubmission(Survey $survey, SurveyTaken $surveyTaken, User $user = null): void;
    public function calculateStatistics(Survey $survey): array;
    public function checkAccess(User $user, Survey $survey): bool;
    public function prepareResults(Survey $survey): array;
    public function generateCsvResults(Survey $survey): string;
}
```

**Methods to Extract:**
1. `showAction()` execution logic (lines 42-82)
2. `showUserMainAction()` private method (lines 132-199)
3. `ensureAccess()` private method (lines 487-503)
4. `showSurveysAction()` statistics (lines 312-353)
5. `resultSurveyAction()` preparation (lines 409-426)
6. `getSurveyResultCSVAction()` generation (lines 448-454)

---

### AssistantController - Detailed Analysis

**Priority:** 🔴 ADMISSION WORKFLOW

**Extract to AdmissionService:**
```php
interface AdmissionServiceInterface
{
    public function prepareAdmissionPageData(Department $specificDepartment = null): array;
    public function submitApplication(Application $application, Department $department): Application;
    public function findDepartmentByLocation(string $location): ?Department;
    public function getTeamsForDepartment(Department $department): array;
    public function validateAdmissionPeriod(Department $department): ?AdmissionPeriod;
}
```

---

## Extraction Patterns Identified

### Pattern 1: Workflow Services
Controllers that orchestrate multi-step business processes:
- InterviewController → InterviewSchedulingService
- SurveyController → SurveyExecutionService
- AssistantController → AdmissionService
- ProfileController → ProfileService

### Pattern 2: Data Aggregation Services
Controllers that compile data from multiple sources:
- AdmissionAdminController → AdmissionManagementService
- ProfileController → ProfileService
- TeamAdminController → TeamManagementService
- ReceiptController → ReceiptStatisticsService

### Pattern 3: CRUD Enhancement Services
Controllers with simple CRUD but workflow logic:
- UserAdminController → UserManagementService
- TeamAdminController → TeamManagementService

---

## Metrics Summary

### Controllers Analysis
- **Total Controllers:** 62
- **Already Refactored (DI):** 10 (16%)
- **Need Business Logic Extraction:** 28 (45%)
- **Simple CRUD (Low Priority):** 24 (39%)

### Business Logic Extraction Status
- **✅ Completed:** 3 services (HomeService, PartnerService, ArticleService)
- **🔴 High Priority:** 9 controllers need extraction
- **🟡 Medium Priority:** 12 controllers need extraction
- **🟢 Low Priority:** ✅ 7 controllers reviewed - no extraction needed

### Service Locator Usage
- **Total Service Locator Calls:** ~370 across 51 files
- **In Controllers:** ~200+ calls in controllers needing extraction
- **Target:** 0 calls (100% dependency injection)

---

## Recommended Extraction Order

### Phase 1: Critical Workflows (High Priority)
1. InterviewController → InterviewSchedulingService
2. SurveyController → SurveyExecutionService
3. AssistantController → AdmissionService
4. AdmissionAdminController → AdmissionManagementService

### Phase 2: User-Facing Operations
5. ProfileController → ProfileService
6. UserAdminController → UserManagementService
7. TeamAdminController → TeamManagementService

### Phase 3: Statistics & Reporting
8. ReceiptController → ReceiptStatisticsService
9. ApplicationStatisticsController → (verify if more needed)

### Phase 4: Admin Operations (Medium Priority)
10. SchoolAdminController
11. ArticleAdminController
12. DepartmentController
13. ExecutiveBoardController

---

## Implementation Guidelines

### For Each Extraction:

1. **Create Service Interface**
   - Define clear method signatures
   - Document parameters and return types
   - Use PHPDoc for complex return arrays

2. **Implement Service**
   - Inject all dependencies (repositories, other services)
   - Use repository interfaces, not Doctrine directly
   - Keep methods focused and single-purpose

3. **Update Controller**
   - Inject service via constructor
   - Simplify controller methods (< 15 lines ideally)
   - Remove all service locator calls
   - Keep HTTP concerns in controller (redirects, flash messages, rendering)

4. **Update Services Configuration**
   - Register service in `services.yml`
   - Create interface alias
   - Ensure autowiring works

5. **Test**
   - Manual testing of affected functionality
   - Update existing tests if present
   - Add service unit tests

---

## Notes

- **ReceiptController** and **FeedbackController** are already well-refactored with dependency injection, but could still benefit from extracting workflow logic into services.

- **ApplicationStatisticsController** is already refactored and uses services properly - minimal extraction needed.

- Some controllers like **InterviewController** already use services (InterviewManager, ApplicationManager) but have additional business logic that should also be extracted.

- Focus on extracting **workflows**, **calculations**, and **data aggregation** - keep **HTTP concerns** (redirects, flash messages, form handling) in controllers.

---

## Next Steps

1. **Review this analysis** with the team
2. **Prioritize** based on business impact
3. **Start with Phase 1** (Critical Workflows)
4. **Track progress** in MIGRATION_STATUS.md
5. **Update** after each extraction

---

**Last Updated:** Current Session  
**Analysis Completed By:** Auto (AI Assistant)
