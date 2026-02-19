# Homepage Migration Design

## Goal

Connect the v2 React homepage to the monolith's data via API Platform, replacing all hardcoded data with live API calls. Full scope: all pages including teams, contact, and application/contact form submissions.

## Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Scope | Full v2 homepage (all pages) | User preference |
| Architecture | API Platform for everything | Consistent with Phase 1, auto-docs |
| Application form | Full API submission | POST /api/applications via state processor |
| Contact form | Full API submission | POST /api/contact_messages via DTO + processor |
| Geolocation | Client-side (ipinfo.io in browser) | Simpler, no server proxy |
| FAQs | Keep hardcoded in v2 | No FAQ entity in monolith DB |

## Architecture

Extend the Phase 1 API Platform setup. All endpoints live under `/api/` with auto-generated OpenAPI docs. Read endpoints use `#[ApiResource]` with serialization groups. Write endpoints use DTOs with custom state processors.

Client-side: v2 homepage `src/api/*.ts` files are rewritten from hardcoded returns to `fetch()` calls against `VITE_API_BASE_URL`. React Router loaders handle data fetching with fallback data for resilience.

## Entities to Expose

### Already Exposed (Phase 1)

| Entity | Endpoints | Groups |
|--------|-----------|--------|
| Sponsor | GetCollection, Get | `sponsor:read` |
| Article | GetCollection, Get | `article:read` |
| StaticContent | GetCollection, Get | `static_content:read` |

### New Read-Only

| Entity | Endpoints | Groups | Filters | Notes |
|--------|-----------|--------|---------|-------|
| Department | GetCollection, Get | `department:read` | `active`, `city` | Embed current admission period + info meeting via group `department:detail` on Get |
| AdmissionPeriod | GetCollection, Get | `admission:read` | `department` | Only expose current/future periods |
| InfoMeeting | GetCollection, Get | `info_meeting:read` | `showOnPage` | Linked from AdmissionPeriod |
| Team | GetCollection, Get | `team:read` | `department`, `active` | Embed members on Get via `team:detail` |
| TeamMembership | GetCollection | `team_member:read` | `team`, `team.department` | Expose user name, role, photo via groups |
| FieldOfStudy | GetCollection | `field_of_study:read` | `department` | Needed for application form dropdown |

### Custom Endpoints (API Platform operations with custom providers/processors)

| Endpoint | Method | Type | Description |
|----------|--------|------|-------------|
| `/api/statistics` | GET | Custom provider | Returns `{ assistantCount, teamMemberCount, femaleAssistantCount, maleAssistantCount }` |
| `/api/applications` | POST | DTO + processor | Assistant application submission. Creates Application entity, sends confirmation email |
| `/api/contact_messages` | POST | DTO + processor | Contact form submission. Sends email to department |

## Serialization Strategy

### Groups Pattern

Each entity gets a `{name}:read` group for collection views (scalar fields only) and optionally a `{name}:detail` group for single-item views that embed one level of relations.

Example for Department:
- `department:read`: id, name, shortName, email, city, address, latitude, longitude, active, logoPath
- `department:detail`: includes `department:read` + currentAdmissionPeriod (with infoMeeting)

### Circular Reference Prevention

Relations are only serialized when the target entity has matching groups. E.g., Department's `teams` collection gets `department:detail` group, but Team's `department` field does NOT get `team:read` group (preventing Department→Team→Department loop).

## Security

| Endpoint Pattern | Access |
|------------------|--------|
| `GET /api/departments*` | PUBLIC_ACCESS |
| `GET /api/admission_periods*` | PUBLIC_ACCESS |
| `GET /api/info_meetings*` | PUBLIC_ACCESS |
| `GET /api/teams*` | PUBLIC_ACCESS |
| `GET /api/team_memberships*` | PUBLIC_ACCESS |
| `GET /api/statistics*` | PUBLIC_ACCESS |
| `GET /api/field_of_studies*` | PUBLIC_ACCESS |
| `POST /api/applications` | PUBLIC_ACCESS |
| `POST /api/contact_messages` | PUBLIC_ACCESS |

All homepage-facing endpoints are public because the homepage is public.

## Write Endpoints Detail

### POST /api/applications

**Input DTO:**
```
ApplicationInput {
    firstName: string (required)
    lastName: string (required)
    email: string (required, valid email)
    phone: string (required)
    fieldOfStudyId: int (required, must exist)
    yearOfStudy: string (required)
    gender: int (required, 0=male 1=female)
    monday-friday: bool (availability)
    substitute: bool
    language: string
    doublePosition: bool
    preferredSchool: string (optional)
    preferredGroup: string (optional)
    departmentId: int (required, must have active admission)
}
```

**Processor logic:**
1. Validate all fields
2. Find or create User by email
3. Find active AdmissionPeriod for department
4. Create Application entity linked to AdmissionPeriod + User
5. Send confirmation email via Symfony Mailer
6. Return 201 with application ID

### POST /api/contact_messages

**Input DTO:**
```
ContactMessageInput {
    name: string (required)
    email: string (required, valid email)
    departmentId: int (required)
    subject: string (required)
    message: string (required)
}
```

**Processor logic:**
1. Validate all fields
2. Find Department
3. Send email to department.email via Symfony Mailer
4. Return 201

No entity created — just sends an email (matches current monolith behavior).

## Frontend Integration

### v2 Homepage API Module Rewrites

| File | Current | New |
|------|---------|-----|
| `src/api/sponsor.ts` | Already uses API | No change needed |
| `src/api/om-oss.ts` | Hardcoded content | Fetch from `/api/static_contents?htmlId=om-oss-*` |
| `src/api/foreldre.ts` | Hardcoded content | Fetch from `/api/static_contents?htmlId=foreldre-*` |
| `src/api/assistenter.ts` | Hardcoded content | Fetch from `/api/static_contents?htmlId=assistenter-*` |
| `src/api/faq.ts` | Hardcoded FAQs | Keep hardcoded (no FAQ entity in monolith) |
| `src/api/team.ts` | Hardcoded team data | Fetch from `/api/teams?department={id}` |
| `src/api/kontakt.ts` | Hardcoded contact info | Fetch from `/api/departments/{id}` with team members |
| NEW `src/api/statistics.ts` | Hardcoded numbers | Fetch from `/api/statistics` |
| NEW `src/api/departments.ts` | N/A | Fetch from `/api/departments` for admission banner |
| NEW `src/api/applications.ts` | N/A | POST to `/api/applications` |
| NEW `src/api/contact.ts` | N/A | POST to `/api/contact_messages` |

### Static Content Mapping

The monolith's StaticContent entity stores HTML by `htmlId`. Content pages (om-oss, foreldre, assistenter) currently have their text hardcoded in v2. Two options:

1. **Seed StaticContent rows** in the monolith DB matching the v2 page content, then fetch via API
2. **Keep content in v2** since it's effectively static marketing copy that changes rarely

Recommendation: Keep FAQ and page content hardcoded in v2 for now. The StaticContent API exists for the homepage tagline and any admin-editable content. Page content migration can happen when a CMS admin UI is built.

### Geolocation Flow

```
Browser → ipinfo.io (direct, client JS)
    ↓ coordinates
GET /api/departments?latitude={lat}&longitude={lon}&active_admission=true
    ↓ departments sorted by distance
Render admission banner for nearest department
```

Department sorting by distance is done client-side using the Haversine formula (same as monolith's GeoLocation service but in TypeScript).

## Phasing

### Phase 2A: Read-Only API (backend)
1. Add `#[ApiResource]` to Department, Team, TeamMembership, AdmissionPeriod, InfoMeeting, FieldOfStudy
2. Add serialization groups
3. Add API Platform filters
4. Create Statistics custom provider
5. Update security.yml access control
6. Write API tests

### Phase 2B: Write API (backend)
7. Create ApplicationInput DTO + state processor
8. Create ContactMessageInput DTO + state processor
9. Write tests for form submissions

### Phase 2C: Frontend Integration (v2 homepage)
10. Rewrite `src/api/*.ts` modules to use real API
11. Update React Router loaders
12. Add geolocation utility
13. Wire up application form
14. Wire up contact form
15. Test end-to-end

## Open Questions

- **Team member photos**: v2 uses `vektorprogrammet.no/media/cache/...` URLs. Are these served by the monolith? Need to confirm image URL strategy.
- **Application email templates**: Need to check what the existing AdmissionController sends on application submission.
- **Content seeding**: Some v2 pages reference content that may not exist as StaticContent rows. Need to verify what `htmlId` values exist in the DB.
