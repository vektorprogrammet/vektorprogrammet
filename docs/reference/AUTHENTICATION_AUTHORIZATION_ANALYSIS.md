# Authentication & Authorization System Analysis

**Created:** 2025-01-28  
**Task:** 14 - Authentication & Authorization Migration  
**Status:** Phase 1 - Analysis Complete

---

## Table of Contents

1. [Current Authentication System](#current-authentication-system)
2. [Current Authorization System](#current-authorization-system)
3. [Laravel Authentication Design](#laravel-authentication-design)
4. [Laravel Authorization Design](#laravel-authorization-design)
5. [Migration Strategy](#migration-strategy)

---

## Current Authentication System

### 1. Security Configuration

**File:** `app/config/security.yml`

#### Password Encoding
- **Algorithm:** bcrypt
- **Cost:** 12
- **Target Entity:** `AppBundle\Entity\User`

#### Role Hierarchy
```yaml
ROLE_TEAM_MEMBER:       ROLE_USER
ROLE_TEAM_LEADER: [ROLE_USER, ROLE_TEAM_MEMBER, ROLE_ALLOWED_TO_SWITCH]
ROLE_ADMIN: [ROLE_USER, ROLE_TEAM_MEMBER, ROLE_TEAM_LEADER, ROLE_ALLOWED_TO_SWITCH]
```

**Roles (from `src/AppBundle/Role/Roles.php`):**
- `ROLE_USER` (ASSISTANT) - Base role
- `ROLE_TEAM_MEMBER` - Team member
- `ROLE_TEAM_LEADER` - Team leader
- `ROLE_ADMIN` - Admin

**Aliases:**
- `assistant` → `ROLE_USER`
- `team_member` → `ROLE_TEAM_MEMBER`
- `team_leader` → `ROLE_TEAM_LEADER`
- `admin` → `ROLE_ADMIN`

### 2. User Providers

**Chain Provider (`chain_provider`):**
- Combines multiple providers in order
- Supports authentication via:
  1. **Username** (`db_username`) - `user_name` property
  2. **Email** (`db_email`) - `email` property
  3. **Company Email** (`db_company_email`) - `companyEmail` property

**Entity:** `AppBundle\Entity\User` (implements `AdvancedUserInterface`)

### 3. Firewalls

#### 3.1 Dev Firewall
- **Pattern:** `^/(_(profiler|wdt)|css|images|js)/`
- **Security:** `false` (no authentication)
- **Purpose:** Static assets and Symfony profiler

#### 3.2 Admin Area Firewall
- **Pattern:** `^/admin`
- **HTTP Basic Auth:** Enabled
- **Logout on User Change:** `true`
- **Provider:** `chain_provider`

#### 3.3 Secured Area Firewall (Main Application)
- **Pattern:** Matches all routes not caught by other firewalls
- **Anonymous:** Enabled (allows anonymous access)
- **HTTP Basic Auth:** Enabled
- **Form Login:**
  - Login path: `/login`
  - Check path: `/login_check`
  - Default target: `/login/redirect`
  - Always use default target: `false`
  - Require previous session: `false`
- **Remember Me:**
  - Secret: `%secret%` (from parameters)
  - Cookie name: `%remember_me.name%` (VPREMEMBER)
  - Lifetime: `31536000` seconds (1 year)
  - Path: `/`
- **Logout:**
  - Path: `/logout`
  - Target: `/`
- **Logout on User Change:** `true`
- **Provider:** `chain_provider`

### 4. User Interface Implementation

**File:** `src/AppBundle/Entity/User.php`

The User entity implements:
- `AdvancedUserInterface` (Symfony)
- `EquatableInterface` (Symfony)
- `Serializable`

**Key Methods:**
- `getPassword()` - Returns hashed password
- `getRoles()` - Returns array of Role entities
- `getSalt()` - Returns null (bcrypt doesn't need salt)
- `getUsername()` - Returns `user_name`
- `eraseCredentials()` - No-op
- `isAccountNonExpired()` - Always returns `true`
- `isAccountNonLocked()` - Always returns `true`
- `isCredentialsNonExpired()` - Always returns `true`
- `isEnabled()` - Returns `isActive` property
- `isEqualTo(UserInterface $user)` - Compares password and username

**Password Setting:**
- Uses `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`
- Password is set via `setPassword()` method

### 5. Authentication Controllers

#### 5.1 SecurityController (`src/AppBundle/Controller/SecurityController.php`)
- **Login Action:** Renders login form
- **Login Check:** Handled by Symfony firewall (`/login_check`)
- **Login Redirect:** Redirects based on user role:
  - `ROLE_TEAM_MEMBER` → Control panel
  - User with active application → My page
  - Otherwise → Profile page

#### 5.2 API AccountController (`src/AppBundle/Controller/Api/AccountController.php`)
- **Login:** `/api/account/login` (GET/POST)
  - Accepts `username` and `password`
  - Authenticates via username or email
  - Creates `UsernamePasswordToken`
  - Stores token in session: `_security_secured_area`
  - Returns JSON with user DTO
- **Logout:** `/api/account/logout` (POST)
  - Clears token storage
  - Returns JSON response
- **Get User:** `/api/account/user` (GET)
  - Returns current authenticated user as DTO

#### 5.3 SSO Controller (`src/AppBundle/Controller/SsoController.php`)
- **Login:** `/sso/login` (GET/POST)
  - Validates credentials
  - **Additional Check:** User must have active team memberships
  - Returns JSON with user info (name, username, email, companyEmail)
  - Does NOT create session (for SSO purposes)

### 6. Password Reset System

#### 6.1 PasswordManager Service (`src/AppBundle/Service/PasswordManager.php`)

**Methods:**
- `generateRandomResetCode()` - Creates 24-character hex string
- `hashCode($resetCode)` - SHA-512 hash of reset code
- `resetCodeIsValid($resetCode)` - Checks if code exists and has user
- `resetCodeHasExpired($resetCode)` - Checks if > 1 day old (auto-deletes if expired)
- `getPasswordResetByResetCode($resetCode)` - Retrieves PasswordReset entity
- `createPasswordResetEntity($email)` - Creates new reset entity for email
- `sendResetCode(PasswordReset $passwordReset)` - Sends email with reset code

**Password Reset Entity:**
- Stores hashed reset code in database
- Links to User entity
- Has reset time for expiration check
- Expires after 1 day

#### 6.2 PasswordResetController (`src/AppBundle/Controller/PasswordResetController.php`)

**Actions:**
1. **Show:** `/reset_password` (GET)
   - Displays form to request password reset
   - Validates email
   - Rejects company email (@vektorprogrammet.no)
   - Rejects inactive users
   - Deletes old password resets for user
   - Creates new password reset
   - Sends email
   
2. **Confirmation:** `/reset_password_confirmation` (GET)
   - Shows confirmation page
   
3. **Reset:** `/reset_password/{resetCode}` (GET/POST)
   - Validates reset code
   - Checks expiration
   - Shows form to set new password
   - Updates user password
   - Deletes password reset entity
   - Redirects to login

**Email Template:**
- `app/Resources/views/reset_password/new_password_email.txt.twig`
- Contains reset link with reset code

### 7. Remember Me Functionality

- **Cookie Name:** `VPREMEMBER` (configurable via `%remember_me.name%`)
- **Lifetime:** 1 year (31536000 seconds)
- **Path:** `/`
- **Implementation:** Symfony's built-in remember me feature
- **Usage:** Checkbox on login form (hidden input set to `true` by default)

**Files:**
- `app/Resources/views/login/login.html.twig` - Login form
- `app/Resources/views/base/user_dropdown.html.twig` - User dropdown

---

## Current Authorization System

### 1. Access Control Rules (security.yml)

**File:** `app/config/security.yml` (lines 73-158)

Access control rules defined by path patterns and roles:

#### ROLE_ADMIN Routes
- `/kontrollpanel/admin`
- `/kontrollpanel/semesteradmin`
- `/kontrollpanel/avdelingadmin/opprett`
- `/kontrollpanel/avdelingadmin/slett`
- `/kontrollpanel/teamadmin/stilling/slett`
- `/kontrollpanel/skoleadmin/slett`
- `/kontrollpanel/intervju/slett`
- `/kontrollpanel/opptakadmin/slett`
- `/kontrollpanel/bruker/vekorepost/endre`
- `/kontrollpanel/hovedstyret/slett/bruker`

#### ROLE_TEAM_LEADER Routes
- Multiple `/kontrollpanel/*` routes
- `/profile/aktiv`, `/profile/deaktiv`, `/profile/rolle/endre`
- `/kontrollpanel/profil/rediger`
- Various admin panel routes for team management
- `/updatestaticcontent`

#### ROLE_TEAM_MEMBER Routes
- `/signatures`
- `/kontrollpanel/attest`, `/profile/attest`
- `/profile/edit`
- `/efconnect`, `/elfinder` (file manager)
- `/kontrollpanel`
- `/api/party`

#### ROLE_USER Routes
- `/profil/rediger`, `/profil/partnere`
- `/profile` (exact match)
- `/utlegg`
- `/api/myreceipts`
- `/min-side`
- `/eksisterendeopptak` (POST only)

### 2. AccessControlService

**File:** `src/AppBundle/Service/AccessControlService.php`

#### Database-Driven Access Rules

The service implements a sophisticated access control system that extends beyond static YAML rules:

**AccessRule Entity Structure:**
- **Resource:** Route name or path pattern
- **Method:** HTTP method (GET, POST, etc.)
- **Users:** Specific users allowed
- **Teams:** Teams allowed
- **Roles:** Roles allowed
- **Executive Board:** Boolean flag for executive board access
- **Is Routing Rule:** Whether it's a route name vs path

**Access Check Logic:**
1. If no rules exist → Access granted (logs as unhandled)
2. If empty rule exists → Access granted
3. If user is null or has no roles → Access denied
4. If user is admin → Access granted
5. For each rule:
   - Check user list (if specified, user must be in list)
   - Check team/executive board access (if specified)
   - Check role access (if specified)
   - If user matches any rule → Access granted

**Caching:**
- Preloads all access rules and unhandled rules into memory cache
- Cache key: `{method}-{resource}`

**Unhandled Rules:**
- Tracks routes accessed without access rules
- Stored in `UnhandledAccessRule` entity
- Used for identifying missing access control

### 3. AccessControlSubscriber

**File:** `src/AppBundle/EventSubscriber/AccessControlSubscriber.php`

**Purpose:** Event subscriber that checks access on every request

**Implementation:**
- Listens to `KernelEvents::REQUEST` event
- Only processes master requests
- Gets route name and HTTP method from request
- Calls `AccessControlService::checkAccess()`
- Throws `AccessDeniedHttpException` if access denied

**Result:** Every route is automatically checked against access rules before controller execution.

### 4. RoleManager Service

**File:** `src/AppBundle/Service/RoleManager.php`

**Functionality:**
- Validates roles and aliases
- Maps aliases to roles
- Checks if logged-in user can create/change roles
- Determines user's role level
- Auto-updates user roles based on team membership

**Key Methods:**
- `isValidRole($role)` - Validates role string
- `canChangeToRole($role)` - Checks if role can be changed (not ADMIN)
- `mapAliasToRole($alias)` - Converts alias to role constant
- `loggedInUserCanCreateUserWithRole($role)` - Authorization check
- `loggedInUserCanChangeRoleOfUsersWithRole($user, $role)` - Authorization check
- `userIsGranted($user, $role)` - Checks if user has role level
- `updateUserRole($user)` - Auto-updates role based on:
  - Executive board membership → `ROLE_TEAM_LEADER`
  - Team leader membership → `ROLE_TEAM_LEADER`
  - Team member membership → `ROLE_TEAM_MEMBER`
  - Otherwise → `ROLE_ASSISTANT` (ROLE_USER)

**Role Hierarchy Check:**
- Roles are ordered: ASSISTANT < TEAM_MEMBER < TEAM_LEADER < ADMIN
- User has access if their role level >= required role level

---

## Laravel Authentication Design

### 1. Authentication Guards

**Configuration File:** `laravel-app/config/auth.php`

#### Web Guard (Primary)
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

**Purpose:** Session-based authentication for web application

#### Provider Configuration
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

**Custom User Provider:** Need to support multiple authentication fields (username, email, company email)

### 2. User Model Updates

**Current Status:** `laravel-app/app/Models/User.php` already extends `Authenticatable`

**Required Updates:**
1. **Custom Username Field:**
   ```php
   public function getAuthIdentifierName()
   {
       return 'user_name'; // Default is 'id'
   }
   ```

2. **Multiple Authentication Fields:**
   - Create custom user provider OR
   - Override `findForAuthentication()` method in repository

3. **Password Hashing:**
   - Already handled by Laravel's `Hash::make()` or `bcrypt()`
   - Ensure compatibility with existing bcrypt hashes (cost 12)

4. **Remember Me:**
   - Laravel handles automatically via `remember_token` field
   - May need to add migration if column doesn't exist

### 3. Authentication Controllers

#### 3.1 Login Controller
**Location:** `laravel-app/app/Http/Controllers/Auth/LoginController.php`

**Features:**
- Form login (`/login`)
- API login (`/api/account/login`)
- Remember me support
- Custom redirect logic based on roles

**Implementation:**
```php
// Use Laravel's AuthenticatesUsers trait
use Illuminate\Foundation\Auth\AuthenticatesUsers;

public function authenticated(Request $request, $user)
{
    // Custom redirect logic
    if ($user->hasRole(Roles::TEAM_MEMBER)) {
        return redirect()->route('control_panel');
    }
    // ... etc
}
```

#### 3.2 Logout Controller
- Use Laravel's built-in logout
- Clear session
- Invalidate remember me cookie

#### 3.3 Password Reset Controller
**Location:** `laravel-app/app/Http/Controllers/Auth/ForgotPasswordController.php`  
**Location:** `laravel-app/app/Http/Controllers/Auth/ResetPasswordController.php`

**Options:**
1. **Use Laravel's Built-in:** 
   - Already has password reset tables
   - May need to adapt to existing PasswordReset entity

2. **Keep Custom Implementation:**
   - Maintain existing PasswordManager service
   - Adapt controllers to use Laravel's auth middleware
   - Keep existing email templates

**Recommendation:** Use Laravel's built-in but adapt to existing database schema if PasswordReset entity is different.

### 4. Middleware

#### 4.1 Authentication Middleware
- `auth` middleware (built-in)
- `auth:web` (explicit guard)

#### 4.2 Remember Me
- Automatic via Laravel's session guard
- Configure cookie name in `config/session.php` or `config/auth.php`

### 5. Session Configuration

**Requirements:**
- Share sessions with Symfony (during migration)
- Same session driver (database/file/redis)
- Same session name and domain

**Configuration:** `laravel-app/config/session.php`

---

## Laravel Authorization Design

### 1. Role System Approach

#### Option 1: Laravel Gates (Recommended for Start)
**Pros:**
- Built-in, no dependencies
- Simple role checks
- Good for hierarchical roles

**Implementation:**
```php
// app/Providers/AuthServiceProvider.php
Gate::define('is-admin', function ($user) {
    return $user->hasRole(Roles::ADMIN);
});

Gate::define('is-team-leader', function ($user) {
    return $user->hasRole(Roles::TEAM_LEADER);
});

Gate::define('is-team-member', function ($user) {
    return $user->hasRole(Roles::TEAM_MEMBER);
});
```

**Usage:**
```php
// In controllers
if (Gate::denies('is-team-leader')) {
    abort(403);
}

// In routes
Route::middleware('can:is-team-leader')->group(function () {
    // ...
});
```

#### Option 2: Laravel Policies
**Best for:** Model-based authorization (e.g., can user edit this specific team?)

**Implementation:**
- Create policies for models
- Use for resource-specific checks

#### Option 3: Spatie Permission Package
**Best for:** Full-featured permission system
**Consideration:** May be overkill for current needs

**Recommendation:** Start with Gates, add Policies for model-specific checks.

### 2. Access Control Migration Strategy

#### 2.1 Static Rules (from security.yml)

**Convert to Route Middleware Groups:**

```php
// routes/web.php

// Admin routes
Route::prefix('kontrollpanel')
    ->middleware(['auth', 'can:is-admin'])
    ->group(function () {
        Route::get('/admin', ...);
        // ... other admin routes
    });

// Team Leader routes
Route::prefix('kontrollpanel')
    ->middleware(['auth', 'can:is-team-leader'])
    ->group(function () {
        Route::get('/teamadmin', ...);
        // ... other team leader routes
    });

// Team Member routes
Route::middleware(['auth', 'can:is-team-member'])
    ->group(function () {
        Route::get('/kontrollpanel', ...);
        // ...
    });
```

#### 2.2 Dynamic Rules (AccessControlService)

**Option 1: Middleware-Based**
- Create `CheckAccessRule` middleware
- Check database rules on each request
- Similar to AccessControlSubscriber

**Option 2: Gate-Based**
- Create dynamic gates from database rules
- Register gates in `AuthServiceProvider`
- Cache gate definitions

**Implementation:**
```php
// app/Http/Middleware/CheckAccessRule.php
class CheckAccessRule
{
    public function handle($request, Closure $next)
    {
        $route = $request->route()->getName();
        $method = $request->method();
        
        if (!$this->accessControlService->checkAccess([$route => $method])) {
            abort(403);
        }
        
        return $next($request);
    }
}
```

**Route Usage:**
```php
Route::middleware(['auth', 'check.access.rule'])->group(function () {
    // All routes
});
```

### 3. Role Helper Methods

**Add to User Model:**
```php
public function hasRole(string $role): bool
{
    return $this->roles()
        ->where('role', $role)
        ->exists();
}

public function hasAnyRole(array $roles): bool
{
    return $this->roles()
        ->whereIn('role', $roles)
        ->exists();
}

public function isAdmin(): bool
{
    return $this->hasRole(Roles::ADMIN);
}

public function isTeamLeader(): bool
{
    return $this->hasRole(Roles::TEAM_LEADER);
}

public function isTeamMember(): bool
{
    return $this->hasRole(Roles::TEAM_MEMBER);
}
```

### 4. Access Control Service Migration

**Keep Service:** Maintain `AccessControlService` but adapt for Laravel

**Changes:**
- Use Laravel's Auth facade instead of Symfony TokenStorage
- Use Laravel's Request instead of Symfony Request
- Return responses using Laravel's response helpers

---

## Migration Strategy

### Phase 1: Analysis ✅ COMPLETE
- [x] Document authentication system
- [x] Document authorization system
- [x] Design Laravel authentication
- [x] Design Laravel authorization
- [x] Create migration strategy

### Phase 2: Preparation (Can Start After Service Integration)

#### 2.1 User Model Enhancements
- [ ] Add `getAuthIdentifierName()` method
- [ ] Add role helper methods (`hasRole()`, `isAdmin()`, etc.)
- [ ] Ensure password hashing compatibility
- [ ] Add `remember_token` migration if needed

#### 2.2 Authentication Infrastructure
- [ ] Create custom user provider for multiple auth fields (username/email/company email)
- [ ] Configure auth guards in `config/auth.php`
- [ ] Test password verification with existing bcrypt hashes

#### 2.3 Authorization Infrastructure
- [ ] Create Gates in `AuthServiceProvider`
- [ ] Create `CheckAccessRule` middleware
- [ ] Migrate AccessControlService to Laravel
- [ ] Create route middleware groups for static rules

### Phase 3: Core Authentication Implementation

#### 3.1 Web Authentication
- [ ] Create/update LoginController
- [ ] Create/update LogoutController
- [ ] Implement login redirect logic
- [ ] Test form login
- [ ] Test remember me

#### 3.2 API Authentication
- [ ] Create API login endpoint
- [ ] Create API logout endpoint
- [ ] Create API user info endpoint
- [ ] Test API authentication

#### 3.3 SSO Authentication
- [ ] Create/update SSO login endpoint
- [ ] Maintain active team membership check
- [ ] Test SSO flow

#### 3.4 Password Reset
- [ ] Option A: Migrate to Laravel's built-in
  - Adapt to existing PasswordReset entity
  - Update controllers
- [ ] Option B: Keep custom implementation
  - Update controllers to use Laravel auth
  - Maintain PasswordManager service

### Phase 4: Authorization Implementation

#### 4.1 Static Rules Migration
- [ ] Convert security.yml rules to route middleware
- [ ] Test each role's access to routes
- [ ] Verify role hierarchy works

#### 4.2 Dynamic Rules Migration
- [ ] Migrate AccessControlService
- [ ] Migrate AccessControlSubscriber to middleware
- [ ] Test database-driven access rules
- [ ] Verify team/executive board access

#### 4.3 Role System Migration
- [ ] Migrate RoleManager service
- [ ] Test role checks
- [ ] Test role hierarchy
- [ ] Test role creation/change authorization

### Phase 5: Testing & Parallel Running

#### 5.1 Unit Tests
- [ ] Authentication tests
- [ ] Authorization tests
- [ ] Role tests
- [ ] Access control tests

#### 5.2 Integration Tests
- [ ] Login flow
- [ ] Logout flow
- [ ] Password reset flow
- [ ] Access denied scenarios
- [ ] Role-based access scenarios

#### 5.3 Parallel Running
- [ ] Session sharing configuration
- [ ] Test login in Laravel, access Symfony
- [ ] Test login in Symfony, access Laravel
- [ ] Test logout affects both
- [ ] Test remember me works across both

### Phase 6: Gradual Migration

#### 6.1 Route-by-Route Migration
- [ ] Migrate public routes first
- [ ] Migrate authenticated routes
- [ ] Migrate role-protected routes
- [ ] Test each route migration

#### 6.2 Full Cutover
- [ ] Switch all routes to Laravel
- [ ] Remove Symfony authentication
- [ ] Update documentation
- [ ] Monitor for issues

---

## Technical Considerations

### 1. Session Sharing

**Requirements:**
- Same session driver (database/file/redis)
- Same session name
- Same domain
- Same encryption key (if using encrypted sessions)

**Implementation:**
- Use same session table/driver
- Configure both Symfony and Laravel to use same session storage

### 2. Password Compatibility

**Current:** bcrypt with cost 12
**Laravel:** Uses bcrypt by default (cost 10)

**Solution:**
- Existing passwords will work (bcrypt verifies regardless of cost)
- New passwords will use Laravel's default cost
- Consider migrating passwords gradually (on next login)

### 3. Remember Me Token

**Current:** Symfony's remember me cookie
**Laravel:** Uses `remember_token` column

**Migration:**
- Option 1: Keep Symfony's cookie during migration, migrate gradually
- Option 2: Force re-login when migrating routes

### 4. Access Control Performance

**Current:** AccessControlService caches rules in memory
**Laravel:** Should maintain similar caching strategy

**Implementation:**
- Use Laravel's cache system
- Cache gate definitions
- Cache access rule lookups

### 5. API Authentication

**Current:** Manual token creation in session
**Laravel:** Can use same session-based auth

**Alternative:** Consider Laravel Sanctum for API tokens in future

---

## Dependencies & Blockers

### Current Blockers
- ⏸️ **Task 12 (Eloquent Models):** Need Role model migrated
- ⏸️ **Task 13 (Service Integration):** Need services using Eloquent repositories

### No Blockers for Phase 1 ✅
- Analysis can be completed now
- Design documents can be created
- Migration strategy can be planned

---

## Files Reference

### Symfony Authentication Files
- `app/config/security.yml` - Security configuration
- `src/AppBundle/Entity/User.php` - User entity
- `src/AppBundle/Controller/SecurityController.php` - Login controller
- `src/AppBundle/Controller/Api/AccountController.php` - API auth
- `src/AppBundle/Controller/SsoController.php` - SSO auth
- `src/AppBundle/Controller/PasswordResetController.php` - Password reset

### Symfony Authorization Files
- `src/AppBundle/Service/AccessControlService.php` - Access control
- `src/AppBundle/Service/RoleManager.php` - Role management
- `src/AppBundle/Entity/AccessRule.php` - Access rule entity
- `src/AppBundle/EventSubscriber/AccessControlSubscriber.php` - Access check subscriber
- `src/AppBundle/Role/Roles.php` - Role constants

### Laravel Files (Current)
- `laravel-app/app/Models/User.php` - User model (already migrated)
- `laravel-app/config/auth.php` - Auth configuration (basic)

### Services
- `src/AppBundle/Service/PasswordManager.php` - Password reset service
- `src/AppBundle/Service/Contract/PasswordManagerInterface.php` - Password manager interface

---

## Next Steps

1. ✅ Complete Phase 1 (Analysis) - **DONE**
2. ⏸️ Wait for Task 12 (Eloquent Models) - Role model
3. ⏸️ Wait for Task 13 (Service Integration)
4. → Start Phase 2 (Preparation)
5. → Implement Phase 3 (Authentication)
6. → Implement Phase 4 (Authorization)
7. → Test and migrate gradually

---

**Document Status:** ✅ Complete  
**Ready for:** Design review and implementation planning

