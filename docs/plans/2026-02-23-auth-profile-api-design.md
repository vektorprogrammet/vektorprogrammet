# Auth Profile API Design

## Goal

Expose the authenticated user's profile as a read/write API endpoint, enabling the v2 frontend to eventually replace the Twig-based profile pages. This is the first auth-required API Platform resource.

## Scope

- `GET /api/me` — return the authenticated user's profile
- `PUT /api/me` — update editable profile fields
- `POST /api/login` already exists (LexikJWTAuthenticationBundle) — no changes needed

Out of scope: password reset, admin profile editing, role management, frontend pages.

## Approach: Dedicated DTO

Use a `ProfileResource` DTO (like ContactMessage/Application) rather than exposing the User entity directly. The User entity has sensitive fields (passwordHash, companyEmail, accountNumber, new_user_code) that should never appear in API responses.

## Components

### ProfileResource DTO

`src/App/ApiResource/ProfileResource.php`

| Field | Type | Read | Write | Source |
|-------|------|------|-------|--------|
| id | int | yes | no | User.id |
| firstName | string | yes | yes | User.firstName |
| lastName | string | yes | yes | User.lastName |
| userName | string | yes | no | User.user_name |
| email | string | yes | yes | User.email |
| phone | string\|null | yes | yes | User.phone |
| gender | int | yes | yes | User.gender |
| fieldOfStudy | object\|null | yes | no | User.fieldOfStudy (id + name) |

Operations:
- `Get` item operation at `/api/me` (uriTemplate: `/me`)
- `Put` item operation at `/api/me`
- No collection operation — this is a singleton resource for the current user

### ProfileProvider

`src/App/State/ProfileProvider.php`

- Injects `Security` to get the current user from the JWT token
- Maps `User` entity fields to `ProfileResource`
- Returns 401 if no authenticated user (handled by security config)

### ProfileProcessor

`src/App/State/ProfileProcessor.php`

- Receives `ProfileResource` with updated fields
- Maps writable fields back to the `User` entity
- Validates via Symfony constraints on the DTO
- Persists via EntityManager
- Returns the updated `ProfileResource`

## Security

- Both operations: `security: "is_granted('ROLE_USER')"`
- This is the first non-PUBLIC_ACCESS API Platform endpoint
- The `api` firewall (`^/api`) already has `jwt: ~` configured
- UserChecker already blocks inactive users

## Data Flow

```
Frontend                    API Platform              Domain
--------                    ------------              ------
POST /api/login --------> LexikJWT ----------------> returns JWT
GET /api/me    --------> ProfileProvider -----------> User entity -> ProfileResource -> JSON
PUT /api/me    --------> ProfileProcessor ----------> validate -> update User -> ProfileResource -> JSON
```

## Testing

Functional tests using WebTestCase:
1. `GET /api/me` without token → 401
2. `POST /api/login` with valid credentials → JWT token
3. `GET /api/me` with JWT → profile JSON with expected fields
4. Verify sensitive fields (password, companyEmail, accountNumber) are absent
5. `PUT /api/me` with updated fields → 200 with updated profile
6. `PUT /api/me` with invalid data → 422 validation errors
7. `PUT /api/me` attempting to change read-only fields (id, userName) → ignored

## Risks

- **First auth-required endpoint**: JWT + API Platform security integration hasn't been exercised yet. May need debugging of firewall/security voter configuration.
- **UserChecker interaction**: The existing UserChecker blocks inactive users at login time. Need to verify it also applies to JWT-authenticated requests or handle separately.
