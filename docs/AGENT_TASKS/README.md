# Agent Tasks for Symfony to Laravel Migration

This directory contains detailed task specifications for agents to work on refactoring tasks in parallel.

## Available Tasks

### ✅ Foundation Phase (Complete)

### 1. Repository Interfaces
**File:** `01_REPOSITORY_INTERFACES.md`
**Priority:** High
**Status:** ✅ 84% Complete (32/38 interfaces created)

### 2. Update Repositories
**File:** `02_UPDATE_REPOSITORIES.md`
**Priority:** High
**Status:** ✅ Complete (all created interfaces implemented)

### 3. Service Interfaces
**File:** `03_SERVICE_INTERFACES.md`
**Priority:** High
**Status:** ✅ Complete (32/32 services have interfaces)

### ✅ Injection Phase (Complete)

### 5. Controller Dependency Injection
**File:** `05_CONTROLLER_DEPENDENCY_INJECTION.md`
**Priority:** Medium
**Status:** ✅ Complete (57/57 controllers refactored)

---

### ⏳ Extraction Phase (In Progress)

### 4. Extract Business Logic - High Priority
**File:** `04_EXTRACT_BUSINESS_LOGIC.md`
**Priority:** High
**Status:** ✅ Delegated (9 controllers)

### 6. Extract Business Logic - Medium Priority
**File:** `06_EXTRACT_MEDIUM_PRIORITY_LOGIC.md`
**Priority:** Medium
**Status:** ⏳ Ready for Delegation (8 controllers)

Extract business logic from:
- CertificateController
- SchoolAdminController
- ArticleAdminController
- DepartmentController
- SemesterController
- FieldOfStudyController
- ExecutiveBoardController
- AdmissionPeriodController

### 8. Analyze Additional Controllers
**File:** `08_ANALYZE_ADDITIONAL_CONTROLLERS.md`
**Priority:** Medium
**Status:** ⏳ Ready (5+ controllers)

Analyze for extraction opportunities:
- AssistantSchedulingController
- ExistingUserAdmissionController
- SubstituteController
- TeamApplicationController
- WidgetController

### 9. Extract Business Logic - Low Priority
**File:** `09_EXTRACT_LOW_PRIORITY_LOGIC.md`
**Priority:** Low
**Status:** ⏳ Ready (7 controllers)

Review low-priority controllers for any extraction opportunities.

---

### ⏳ Finalization Phase (Ready)

### 7. Remaining Repository Interfaces
**File:** `07_REMAINING_REPOSITORY_INTERFACES.md`
**Priority:** Low
**Status:** ⏳ Ready (6 repositories remaining)

Complete repository abstraction:
- CertificateRequestRepository
- InfoMeetingRepository
- PositionRepository
- SponsorRepository
- SurveyAnswerRepository
- UserGroupRepository/UserGroupCollectionRepository

## Workflow Recommendation

### ✅ Phase 1: Foundation (Complete)
1. **Agent 01:** Create repository interfaces ✅ (84% - 32/38)
2. **Agent 02:** Update repositories to implement interfaces ✅
3. **Agent 03:** Create service interfaces ✅ (100% - 32/32)

### ✅ Phase 3: Injection (Complete)
5. **Agent 05:** Refactor controllers to use dependency injection ✅ (100% - 57/57)

### ⏳ Phase 2: Extraction (In Progress)
4. **Agent 04:** Extract business logic from high-priority controllers ✅ Delegated (9 controllers)
6. **Agent 06:** Extract business logic from medium-priority controllers ⏳ Ready (8 controllers)
8. **Agent 08:** Analyze additional controllers ⏳ Ready (5+ controllers)
9. **Agent 09:** Review low-priority controllers ⏳ Ready (7 controllers)

### ⏳ Phase 4: Finalization (Ready)
7. **Agent 07:** Complete remaining repository interfaces ⏳ Ready (6 repositories)

**Current Focus:** Business logic extraction from medium-priority controllers

## Task Completion Checklist

When working on a task:

1. **Read the task file thoroughly**
2. **Review examples** in `docs/REFACTORING_EXAMPLES.md` if applicable
3. **Complete one item** at a time (don't batch unless task specifies)
4. **Test your changes** before marking complete
5. **Update progress** in the task file
6. **Commit with clear message** following the pattern in task file
7. **Move to next item** in priority order

## Acceptance Criteria

All tasks should meet:
- ✅ No syntax errors
- ✅ Service container resolves dependencies correctly
- ✅ Existing functionality still works
- ✅ Code follows patterns in examples
- ✅ Tests pass (if they exist)
- ✅ **All parameters have type hints** (see CODING_STANDARDS.md)
- ✅ **All methods have return types** (see CODING_STANDARDS.md)
- ✅ **Complete PHPDoc comments** (see CODING_STANDARDS.md)

## Communication

When working on tasks:
- **Mark progress** in task files
- **Commit frequently** with clear messages
- **Test thoroughly** before marking complete
- **Document any issues** or blockers in task file

## Reference Documents

All agents should be familiar with:
- `docs/SERVICE_EXTRACTION_GUIDE.md` - Overall strategy
- `docs/REFACTORING_EXAMPLES.md` - Code examples
- `docs/ARCHITECTURE_ANALYSIS.md` - System overview
- **`docs/AGENT_TASKS/CODING_STANDARDS.md`** - **MANDATORY coding standards** ⚠️

## ⚠️ Coding Standards

**All agents MUST read and follow:**
- **`docs/AGENT_TASKS/CODING_STANDARDS.md`**

This document specifies mandatory requirements for:
- ✅ Type hints on all parameters
- ✅ Return types on all methods (including `void`)
- ✅ PHPDoc requirements
- ✅ Interface/implementation matching

**Code that doesn't follow these standards will be flagged in reviews.**

## Getting Started

1. Pick a task from the list above
2. Read the corresponding task file
3. Review examples and patterns
4. Start with high-priority items
5. Complete items one at a time
6. Test and commit after each item

## Status Updates

After completing work:
1. Update status in task file (✅ Complete, ⏳ In Progress, ⏸ Blocked)
2. Update this README if task status changes significantly
3. Commit with descriptive message

## Notes

- Work incrementally - small commits are better
- Test each change before moving to next
- Follow existing code patterns
- Ask for clarification if task is unclear
- Don't modify multiple files in one commit unless related

