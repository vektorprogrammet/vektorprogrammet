# Agent Task: Analyze Additional Controllers for Business Logic Extraction

## Objective
Analyze 5+ additional controllers that may need business logic extraction but haven't been fully assessed yet.

## Current Status
- ✅ 9 high-priority controllers analyzed and delegated
- ✅ 8 medium-priority controllers identified and delegated
- ✅ 10 additional controllers analyzed (see `docs/ADDITIONAL_CONTROLLER_ANALYSIS.md`)

## Controllers to Analyze

### 1. AssistantSchedulingController
**File:** `src/AppBundle/Controller/AssistantSchedulingController.php`  
**Priority:** High (complex scheduling logic suspected)

**Analysis Tasks:**
1. Read controller file and identify:
   - Lines of code
   - Number of service locator calls
   - Complex business logic sections
   - Multi-step workflows
   - Data aggregation logic

2. Look for:
   - Scheduling algorithm logic
   - Conflict detection/resolution
   - Assignment workflows
   - Time slot management
   - Availability calculations

3. Document findings in format:
   ```markdown
   ## AssistantSchedulingController
   - **Lines:** [count]
   - **Service Locator Calls:** [count]
   - **Complex Logic Sections:**
     - Line X-Y: [description]
     - Line A-B: [description]
   - **Recommended Service:** [ServiceName]
   - **Methods to Extract:**
     - [method1]: [description]
     - [method2]: [description]
   ```

**Potential Services:**
- `AssistantSchedulingService` - Scheduling algorithm
- `ScheduleConflictResolver` - Conflict detection/resolution

---

### 2. ExistingUserAdmissionController
**File:** `src/AppBundle/Controller/ExistingUserAdmissionController.php`  
**Priority:** Medium-High (admission workflow)

**Analysis Tasks:**
1. Read controller file
2. Identify admission workflow logic
3. Look for user identification/matching logic
4. Identify application creation workflows
5. Check for status management

**Potential Services:**
- `ExistingUserAdmissionService` - Workflow coordination
- `UserApplicationMatcher` - User matching logic

---

### 3. SubstituteController
**File:** `src/AppBundle/Controller/SubstituteController.php`  
**Priority:** Medium (matching logic)

**Analysis Tasks:**
1. Read controller file
2. Identify substitute matching algorithm
3. Look for assignment logic
4. Check for availability matching

**Potential Services:**
- `SubstituteMatchingService` - Matching algorithm
- `SubstituteAssignmentService` - Assignment workflow

---

### 4. TeamApplicationController
**File:** `src/AppBundle/Controller/TeamApplicationController.php`  
**Priority:** Medium (application workflows)

**Analysis Tasks:**
1. Read controller file
2. Identify team application workflows
3. Look for validation logic
4. Check for status transitions

**Potential Services:**
- `TeamApplicationService` - Application workflow
- `TeamApplicationValidator` - Validation logic

---

### 5. WidgetController
**File:** `src/AppBundle/Controller/WidgetController.php`  
**Priority:** Medium (data aggregation)

**Analysis Tasks:**
1. Read controller file
2. Identify widget data aggregation logic
3. Look for statistics calculations
4. Check for multi-entity data joining

**Potential Services:**
- `WidgetDataService` - Data aggregation
- `WidgetStatisticsService` - Statistics calculations

---

### Additional Controllers (Lower Priority)

6. **InterviewSchemaController** - Schema management logic
7. **SurveyNotifierController** - Notification workflows
8. **SurveyPopupController** - Popup display logic
9. **ParticipantHistoryController** - History tracking
10. **MailingListController** - Email list management

---

## Analysis Template

For each controller, create an analysis document with:

```markdown
## [ControllerName] Analysis

### Basic Info
- **File:** `src/AppBundle/Controller/[ControllerName].php`
- **Lines of Code:** [count]
- **Service Locator Calls:** [count]
- **Dependencies Injected:** [list]
- **Still Using Service Locator:** Yes/No

### Business Logic Sections

#### Section 1: [Description]
- **Lines:** X-Y
- **Logic:** [description]
- **Complexity:** Low/Medium/High
- **Dependencies:** [list]
- **Extract to:** [method name]

#### Section 2: [Description]
- [Same format]

### Recommended Services

#### [ServiceName]
**Interface:** `[ServiceName]Interface`

**Methods:**
- `method1(...): returnType` - [description]
- `method2(...): returnType` - [description]

**Dependencies Needed:**
- [RepositoryInterface]
- [ServiceInterface]

### Priority Assessment
- **Priority:** High/Medium/Low
- **Reason:** [justification]
- **Estimated Complexity:** Low/Medium/High
- **Estimated Time:** [estimate]

### Next Steps
- [ ] Create service interface
- [ ] Extract business logic
- [ ] Update controller
- [ ] Test functionality
```

---

## Deliverables

For each controller analyzed, provide:

1. **Analysis Document** - Following template above
2. **Service Interface Design** - If extraction recommended
3. **Extraction Recommendation** - High/Medium/Low priority
4. **Complexity Assessment** - Time estimate

## Output Format

Create a new document or update existing:
- `docs/BUSINESS_LOGIC_EXTRACTION_ANALYSIS.md` - Add sections for these controllers
- Or create: `docs/ADDITIONAL_CONTROLLER_ANALYSIS.md`

## Acceptance Criteria

- [x] All 5 primary controllers analyzed (analyzed 10 total)
- [x] Analysis document created with template (`docs/ADDITIONAL_CONTROLLER_ANALYSIS.md`)
- [x] Service interface designs provided (if extraction recommended)
- [x] Priority assessment completed
- [x] Recommendations documented
- [x] Ready for delegation if high/medium priority

## Completion Summary

**Analysis Complete!** Created comprehensive analysis document at `docs/ADDITIONAL_CONTROLLER_ANALYSIS.md`

**Key Findings:**
- **10 controllers analyzed** (5 primary + 5 additional)
- **4 medium-priority** controllers identified for extraction:
  1. AssistantSchedulingController - Data transformation logic
  2. SubstituteController - Status management + service locator removal needed
  3. TeamApplicationController - Access control + workflow logic
  4. SurveyPopupController - Popup display rules
- **2 controllers** already well-structured (ExistingUserAdmissionController, ParticipantHistoryController)
- **1 controller** still uses service locator pattern (SubstituteController - needs immediate fix)
- **Estimated extraction time:** 8-12 hours total

## Notes

- Focus on identifying **complex workflows**, not simple CRUD
- Look for **multi-step processes** that belong in services
- Consider **reusability** - will logic be used elsewhere?
- Document **data transformations** and **calculations**
- Note any **business rules** that should be extracted


