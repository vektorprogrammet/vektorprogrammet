# Agent Task: Extract Business Logic from Low-Priority Controllers

## ⚠️ Important: Read Standards First
**Before starting, read:**
- `docs/AGENT_TASKS/CODING_STANDARDS.md`
- `docs/AGENT_TASKS/04_EXTRACT_BUSINESS_LOGIC.md`

## Objective
Extract any business logic from 7 low-priority controllers that are primarily CRUD operations but may have extraction opportunities.

## Current Status
- ✅ High-priority controllers complete (9)
- ✅ Medium-priority controllers delegated (8)
- ⏳ Low-priority controllers to review (7)

## Task List

### Controllers to Review

These are primarily CRUD operations but may have small extraction opportunities:

1. **ChangeLogController**
   - File: `src/AppBundle/Controller/ChangeLogController.php`
   - Likely Logic: Simple logging, may have filtering/sorting

2. **StaticContentController**
   - File: `src/AppBundle/Controller/StaticContentController.php`
   - Likely Logic: Content management, versioning (if any)

3. **PositionController**
   - File: `src/AppBundle/Controller/PositionController.php`
   - Likely Logic: Position CRUD, minimal logic

4. **SignatureController**
   - File: `src/AppBundle/Controller/SignatureController.php`
   - Likely Logic: Signature management, validation

5. **SocialEventController**
   - File: `src/AppBundle/Controller/SocialEventController.php`
   - Likely Logic: Event CRUD, date filtering

6. **SponsorsController**
   - File: `src/AppBundle/Controller/SponsorsController.php`
   - Likely Logic: Sponsor management, filtering

7. **AccessRuleController**
   - File: `src/AppBundle/Controller/AccessRuleController.php`
   - Likely Logic: Access rule CRUD, validation

---

## Analysis Approach

For each controller:

1. **Quick Review** (5-10 minutes):
   - Read controller file
   - Count lines of code
   - Check if it's pure CRUD or has business logic

2. **Decision:**
   - **If pure CRUD:** Skip extraction, document as "No extraction needed"
   - **If has logic:** Proceed with extraction

3. **Extraction (if needed):**
   - Follow same pattern as medium-priority controllers
   - Extract minimal logic only
   - Create small, focused services

---

## Implementation Pattern

If extraction is needed, follow the same pattern as other controllers:

1. **Create Service Interface** - Small, focused interface
2. **Create Service Implementation** - Extract logic
3. **Update Controller** - Use service
4. **Update services.yml** - Bind interface

## Acceptance Criteria

- [ ] All 7 controllers reviewed
- [ ] Documentation created showing:
  - Which controllers have extractable logic
  - Which controllers are pure CRUD (no extraction needed)
- [ ] Services created only if logic exists
- [ ] If extraction done, follows all coding standards
- [ ] No unnecessary complexity introduced

## Notes

- **Don't force extraction** - If it's pure CRUD, leave it
- **Keep it simple** - These are low priority for a reason
- **Focus on obvious wins** - Only extract if it's clearly beneficial
- **Document decisions** - Explain why extraction was/wasn't done

## Deliverables

1. **Review Document** - List each controller and decision
2. **Services Created** (if any) - Only if extraction actually needed
3. **Updated Analysis** - Update `BUSINESS_LOGIC_EXTRACTION_ANALYSIS.md` with findings

---

## Example Review Output

```markdown
## Low-Priority Controller Review Results

### ChangeLogController
- **Status:** Pure CRUD, no extraction needed
- **Reason:** Simple logging operations, no complex logic
- **Decision:** Skip

### SignatureController
- **Status:** Minor extraction opportunity
- **Logic:** Signature validation, expiration checks
- **Service Created:** `SignatureValidationService`
- **Decision:** Extracted minimal validation logic

### [Continue for all 7...]
```

