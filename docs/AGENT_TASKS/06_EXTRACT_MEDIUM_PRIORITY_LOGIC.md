# Agent Task: Extract Business Logic from Medium-Priority Controllers

## ⚠️ Important: Read Standards First
**Before starting, read:**
- `docs/AGENT_TASKS/CODING_STANDARDS.md` - Mandatory coding standards
- `docs/AGENT_TASKS/04_EXTRACT_BUSINESS_LOGIC.md` - Base extraction pattern
- `docs/REFACTORING_EXAMPLES.md` - Examples

## Objective
Extract business logic from 8 medium-priority controllers into dedicated service classes following established patterns.

## Current Status
- ✅ High-priority controllers delegated (9 controllers) ✅ **COMPLETE**
- ✅ Medium-priority controllers extracted (8 controllers) ✅ **COMPLETE**

## Task List (Priority Order)

### 1. CertificateController
**File:** `src/AppBundle/Controller/CertificateController.php`  
**Status:** Already uses dependency injection, needs business logic extraction

**Business Logic to Extract:**
- Certificate generation logic (PDF creation with Dompdf)
- Signature retrieval and validation
- Template preparation and rendering
- Certificate data aggregation

**Recommended Service:** `CertificateService`  
**Interface:** `CertificateServiceInterface`

**Extract Methods:**
- `generateCertificate(AssistantHistory $assistantHistory): string` - Generate PDF certificate
- `getSignatureForAssistantHistory(AssistantHistory $assistantHistory): ?Signature` - Retrieve signature
- `prepareCertificateData(AssistantHistory $assistantHistory): array` - Prepare template data

**Dependencies Already Injected:**
- `AssistantHistoryRepositoryInterface`
- `EntityManagerInterface`
- `FileUploaderInterface`

**Additional Dependencies Needed:**
- Consider injecting Dompdf service or create wrapper interface

---

### 2. SchoolAdminController
**File:** `src/AppBundle/Controller/SchoolAdminController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- School capacity management workflows
- School data aggregation and statistics
- Capacity calculations per department/semester
- School-activation logic

**Recommended Service:** `SchoolManagementService`  
**Interface:** `SchoolManagementServiceInterface`

**Extract Methods:**
- `calculateSchoolCapacity(Department $department, Semester $semester): array` - Capacity calculations
- `aggregateSchoolData(Department $department): array` - Data aggregation
- `manageSchoolCapacity(School $school, SchoolCapacity $capacity): void` - Capacity management
- `getSchoolsWithCapacity(Department $department, Semester $semester): array` - Filtering logic

**Dependencies Already Injected:**
- `AssistantHistoryRepositoryInterface`
- `DepartmentRepositoryInterface`
- `UserRepositoryInterface`
- `SchoolRepositoryInterface`
- `EntityManagerInterface`
- `EventDispatcherInterface`

---

### 3. ArticleAdminController
**File:** `src/AppBundle/Controller/ArticleAdminController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Article moderation workflows (approve/reject)
- Article status management (draft/published/archived)
- Publishing workflow coordination
- Article preview/preview logic

**Recommended Service:** `ArticleManagementService`  
**Interface:** `ArticleManagementServiceInterface`

**Extract Methods:**
- `moderateArticle(Article $article, string $action): void` - Moderation workflow
- `publishArticle(Article $article): void` - Publishing workflow
- `updateArticleStatus(Article $article, string $status): void` - Status management
- `prepareArticlePreview(Article $article): array` - Preview data

**Dependencies Already Injected:**
- `ArticleRepositoryInterface`
- `EntityManagerInterface`
- `EventDispatcherInterface` (likely)

---

### 4. DepartmentController
**File:** `src/AppBundle/Controller/DepartmentController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Department data aggregation (stats, teams, admissions)
- Department filtering and sorting logic
- Department activation/deactivation workflows

**Recommended Service:** `DepartmentManagementService`  
**Interface:** `DepartmentManagementServiceInterface`

**Extract Methods:**
- `aggregateDepartmentData(Department $department): array` - Data aggregation
- `filterDepartmentsByCriteria(array $criteria): array` - Filtering logic
- `updateDepartmentStatus(Department $department, bool $active): void` - Status updates

**Dependencies Already Injected:**
- `EntityManagerInterface`

**Additional Dependencies Needed:**
- `DepartmentRepositoryInterface` (inject if not already)
- Consider repositories for related entities (Team, AdmissionPeriod)

---

### 5. SemesterController
**File:** `src/AppBundle/Controller/SemesterController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Semester management workflows (create/update)
- Date range calculations and validation
- Semester overlap detection
- Current/next semester logic

**Recommended Service:** `SemesterManagementService`  
**Interface:** `SemesterManagementServiceInterface`

**Extract Methods:**
- `createSemester(array $data): Semester` - Create with validation
- `calculateDateRanges(DateTime $startDate, DateTime $endDate): array` - Calculations
- `detectOverlappingSemesters(Semester $semester): array` - Overlap detection
- `getCurrentAndNextSemester(): array` - Current/next logic

**Dependencies Already Injected:**
- `SemesterRepositoryInterface`
- `EntityManagerInterface`

---

### 6. FieldOfStudyController
**File:** `src/AppBundle/Controller/FieldOfStudyController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Field of study filtering logic (by department, active status)
- Study program management workflows
- Field of study relationships handling

**Recommended Service:** `FieldOfStudyManagementService`  
**Interface:** `FieldOfStudyManagementServiceInterface`

**Extract Methods:**
- `filterFieldsOfStudy(array $criteria): array` - Filtering logic
- `manageFieldOfStudy(FieldOfStudy $fieldOfStudy, array $data): void` - Management workflow
- `getFieldsByDepartment(Department $department): array` - Department filtering

**Dependencies Already Injected:**
- `EntityManagerInterface`

**Additional Dependencies Needed:**
- `FieldOfStudyRepositoryInterface` (if exists)
- `DepartmentRepositoryInterface`

---

### 7. ExecutiveBoardController
**File:** `src/AppBundle/Controller/ExecutiveBoardController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Board membership management workflows
- Board data aggregation (members, positions, history)
- Board member assignment/removal logic

**Recommended Service:** `ExecutiveBoardManagementService`  
**Interface:** `ExecutiveBoardManagementServiceInterface`

**Extract Methods:**
- `manageBoardMembership(User $user, string $position, DateTime $startDate, ?DateTime $endDate): void` - Membership management
- `aggregateBoardData(): array` - Data aggregation
- `getActiveMembers(): array` - Active members logic

**Dependencies Already Injected:**
- `EntityManagerInterface`
- `RoleManagerInterface`

**Additional Dependencies Needed:**
- `ExecutiveBoardRepositoryInterface`
- `ExecutiveBoardMembershipRepositoryInterface`

---

### 8. AdmissionPeriodController
**File:** `src/AppBundle/Controller/AdmissionPeriodController.php`  
**Status:** Already uses dependency injection

**Business Logic to Extract:**
- Admission period calculations (dates, durations)
- Period validation logic (overlaps, conflicts)
- Active period detection and management

**Recommended Service:** `AdmissionPeriodManagementService`  
**Interface:** `AdmissionPeriodManagementServiceInterface`

**Extract Methods:**
- `calculatePeriodDates(array $data): array` - Date calculations
- `validatePeriod(AdmissionPeriod $period): array` - Validation (returns errors if any)
- `detectConflictingPeriods(AdmissionPeriod $period): array` - Conflict detection
- `getActivePeriods(Department $department): array` - Active periods logic

**Dependencies Already Injected:**
- `AdmissionPeriodRepositoryInterface`
- `EntityManagerInterface`

---

## Implementation Pattern

Follow the same pattern as high-priority controllers (see `docs/AGENT_TASKS/04_EXTRACT_BUSINESS_LOGIC.md`):

1. **Analyze Controller** - Identify business logic vs framework calls
2. **Create Service Interface** - Define contract with proper types
3. **Create Service Implementation** - Extract logic with dependency injection
4. **Update Controller** - Use service instead of inline logic
5. **Update services.yml** - Bind interface to implementation
6. **Test** - Verify functionality unchanged

## Acceptance Criteria

- [x] Service interface created with full type hints and return types ✅
- [x] Service implementation uses dependency injection ✅
- [x] All business logic extracted from controller ✅
- [x] Controller simplified to use service ✅
- [x] Interface bound in `services.yml` ✅
- [x] No service locator calls remain in controller ✅
- [x] Code follows `CODING_STANDARDS.md` ✅
- [x] PHPDoc comments complete ✅
- [x] Functionality verified (manual or automated test) ✅

## ✅ TASK COMPLETE

**Status:** ✅ **All 8 medium-priority controllers extracted**

**Completed Controllers:**
1. ✅ **SchoolAdminController** → `SchoolManagementService` (data aggregation, school creation/deletion, assistant history management)
2. ✅ **ArticleAdminController** → `ArticleManagementService` (article creation, editing, sticky toggle, deletion)
3. ✅ **DepartmentController** → `DepartmentManagementService` (CRUD operations)
4. ✅ **SemesterController** → `SemesterManagementService` (creation with validation, deletion)
5. ✅ **FieldOfStudyController** → `FieldOfStudyManagementService` (data retrieval and persistence)
6. ✅ **ExecutiveBoardController** → `ExecutiveBoardManagementService` (membership management with role updates)
7. ✅ **AdmissionPeriodController** → `AdmissionPeriodManagementService` (creation with validation, update, deletion with info meeting cleanup)
8. ✅ **CertificateController** → Already uses `CertificateService` (no extraction needed)

**All services created and configured:** ✅
- All service interfaces bound in `services.yml`
- All controllers refactored to use services
- All syntax checks passed
- No linter errors

## Testing Strategy

1. **Manual Testing:**
   - Verify each controller action works as before
   - Test service methods independently if possible

2. **Integration Testing:**
   - Ensure service integrates correctly with repositories
   - Verify event dispatching (if applicable)

3. **Code Review Checklist:**
   - Service follows single responsibility principle
   - Dependencies are interfaces, not concrete classes
   - Return types match interface
   - Error handling preserved

## Notes

- These controllers already use dependency injection, so focus is on **logic extraction**
- Preserve existing functionality exactly
- Consider whether extracted methods should be public or private in service
- Document complex business rules in PHPDoc


