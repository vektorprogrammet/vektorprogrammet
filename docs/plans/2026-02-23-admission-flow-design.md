# Admission Flow Migration Design

**Date:** 2026-02-23
**Status:** Approved
**Goal:** Migrate the public admission flow (`/opptak`) from Twig to the v2 React homepage, backed by existing API endpoints.

## Architecture

```
User visits /opptak → v2 React app
  → GET /api/departments (list all)
  → GET /api/departments/{id} (detail: fieldOfStudy[], admissionPeriods[])
  → POST /api/applications (submit form)
  → POST /api/admission_subscribers (subscribe to notifications) [NEW]
```

The v2 frontend determines admission state client-side from `admissionPeriod.startDate` / `endDate`.

## Scope

**In scope:**
- `/opptak` — department-tabbed admission page with form
- `/opptak/{city}` — deep link to specific city tab
- `/opptak/bekreftelse` — confirmation page after submission
- Interest list subscription widget (when admission is closed)
- Info meeting display (when available)

**Out of scope:**
- `/eksisterendeopptak` — existing-user re-application (requires auth)
- `/stand/opptak/{shortName}` — admin/stand form
- Unsubscribe flow (keep in monolith, just a link in email)

## Data Flow

### Department + Admission Period Loading

1. `GET /api/departments` → list all active departments (id, name, shortName, city)
2. For the selected department: `GET /api/departments/{id}` → detail view includes:
   - `fieldOfStudy[]` — dropdown options for the form
   - `admissionPeriods[]` — with startDate, endDate, infoMeeting, semester

### Admission State Machine

The frontend determines what to show based on the current admission period:

| State | Condition | UI |
|-------|-----------|-----|
| **Open** | `now >= startDate && now <= endDate` | Application form + deadline countdown |
| **Upcoming** | `now < startDate` (period exists in current semester) | "Opens on {date}" + subscribe widget |
| **Recently closed** | `now > endDate && now < endDate + 14 days` | "Closed on {date}" + subscribe widget |
| **Closed** | No current period or outside all windows | "Not accepting applications" + subscribe widget |

### Application Form Fields

7 visible fields (matches `POST /api/applications`):

| Field | Type | Validation | Notes |
|-------|------|-----------|-------|
| firstName | text | required | |
| lastName | text | required | |
| email | email | required, email format | |
| phone | tel | required | |
| gender | select | required, 0=Mann 1=Dame | |
| fieldOfStudy | select | required | Filtered by department |
| yearOfStudy | select | required | 1.-5. klasse |

All other `ApplicationInput` fields use defaults (availability days=true, substitute=false, etc.).

### Interest List Subscription

New endpoint needed: `POST /api/admission_subscribers`

**Input DTO:**
```
{ email: string, departmentId: int, infoMeeting: bool }
```

**Behavior:** Creates `AdmissionSubscriber` entity with auto-generated unsubscribe code. Returns 201.

## Frontend Components

All in v2 homepage (`../v2/homepage/`), branch `monolith-merge`.

### Routes

| Route file | URL | Purpose |
|-----------|-----|---------|
| `_home.opptak.tsx` | `/opptak` | Layout with Outlet |
| `_home.opptak._index.tsx` | `/opptak` | Main admission page (default city) |
| `_home.opptak.$city.tsx` | `/opptak/{city}` | Admission page with city pre-selected |
| `_home.opptak.bekreftelse.tsx` | `/opptak/bekreftelse` | Confirmation page |

### API Module

`src/api/admission.ts`:
- `getDepartments()` — cached list of all departments
- `getDepartmentDetail(id)` — single department with fields of study + admission periods
- `submitApplication(data)` — POST to `/api/applications`
- `subscribeToNotifications(data)` — POST to `/api/admission_subscribers`

### Page Structure

The main admission page:
1. **Header** — static content explaining the assistant role
2. **Department tabs** — horizontal tabs (desktop) / select dropdown (mobile), one per active department
3. **Department pane** — switches based on selected tab:
   - If open: application form + deadline display + info meeting banner
   - If closed: subscription widget + info meeting banner (if upcoming)
4. **Info meeting card** — date/time/location when available in the admission period

### Form UX

- Client-side validation matching API constraints
- Field of study dropdown populated from selected department's detail endpoint
- On submit: POST to `/api/applications`, on success redirect to `/opptak/bekreftelse`
- On 422 error: display server validation messages inline
- Loading state on submit button

## Backend Changes

### New: `POST /api/admission_subscribers`

**Files:**
- Create: `src/App/ApiResource/AdmissionSubscriberInput.php`
- Create: `src/App/State/AdmissionSubscriberProcessor.php`

**DTO fields:** `email` (required, email), `departmentId` (required, int), `infoMeeting` (optional, bool, default false)

**Processor:** Look up department, create `AdmissionSubscriber` entity, persist. Return 201. Silently ignore duplicate email+department combos.

### No changes needed to existing endpoints

- `GET /api/departments` — already works
- `GET /api/departments/{id}` — already returns fieldOfStudy + admissionPeriods via detail groups
- `POST /api/applications` — already works, dispatches ApplicationCreatedEvent (sends confirmation email)
- `GET /api/field_of_studies` — available as fallback

## Error Handling

| Error | Frontend behavior |
|-------|------------------|
| API unreachable | Show error banner, disable form |
| 422 from POST /api/applications | Show field-level errors from response |
| 422 "No active admission period" | Switch to closed state, show subscribe widget |
| 422 "Department not found" | Show error, redirect to /opptak |
| Network error on subscribe | Show inline error, allow retry |

## Testing

**Backend:**
- Unit test for `AdmissionSubscriberProcessor` (mock deps)
- API smoke test: POST valid subscription, POST duplicate (idempotent), POST with bad department

**Frontend:**
- Playwright e2e: mock API responses, verify form renders, submit flow, closed-admission state, subscription widget
