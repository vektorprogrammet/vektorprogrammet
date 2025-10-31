# Agent Tasks for Symfony to Laravel Migration

This directory contains detailed task specifications for agents to work on refactoring tasks in parallel.

## Available Tasks

### 1. Repository Interfaces
**File:** `01_REPOSITORY_INTERFACES.md`
**Priority:** High
**Status:** ⏳ In Progress

Create repository interfaces for all major entities. Two examples already exist (Article, User).

**Next Steps:**
- Create interfaces for Department, AdmissionPeriod, Application, Semester, AssistantHistory
- Then continue with remaining repositories

### 2. Update Repositories
**File:** `02_UPDATE_REPOSITORIES.md`
**Priority:** High (after interfaces created)
**Status:** ⏳ Pending

Update existing Doctrine repositories to implement their interfaces.

**Prerequisites:**
- Corresponding interface must exist (from Task 01)

### 3. Service Interfaces
**File:** `03_SERVICE_INTERFACES.md`
**Priority:** High
**Status:** ⏳ Pending

Create interfaces for all 32 services. Two already exist (Mailer, SmsSender).

**Next Steps:**
- Start with high-priority services used by controllers
- Work through medium and low priority

### 4. Extract Business Logic
**File:** `04_EXTRACT_BUSINESS_LOGIC.md`
**Priority:** High
**Status:** ⏳ Pending

Extract complex business logic from controllers into service classes.

**Examples Provided:**
- HomeController → HomeService
- UserController → PartnerService
- ArticleController → ArticleService (pattern)

### 5. Controller Dependency Injection
**File:** `05_CONTROLLER_DEPENDENCY_INJECTION.md`
**Priority:** Medium (after services/repositories refactored)
**Status:** ⏳ Pending

Replace service locator pattern (`$this->get()`) with constructor injection.

**Prerequisites:**
- Repository interfaces created
- Service interfaces created
- Business logic extracted (where applicable)

## Workflow Recommendation

### Phase 1: Foundation (Do First)
1. **Agent 01:** Create repository interfaces (all high priority ones)
2. **Agent 02:** Update repositories to implement interfaces
3. **Agent 03:** Create service interfaces (high priority services first)

### Phase 2: Extraction (Do in Parallel)
4. **Agent 04:** Extract business logic from controllers
   - Can work on different controllers in parallel
   - Start with: HomeController, UserController, ArticleController

### Phase 3: Injection (After Phase 1 & 2)
5. **Agent 05:** Refactor controllers to use dependency injection
   - Requires interfaces from Phase 1
   - Benefits from services extracted in Phase 2

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

