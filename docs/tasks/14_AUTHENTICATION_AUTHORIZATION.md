# Agent Task: Migrate Authentication & Authorization

## Objective
Migrate Symfony authentication and authorization system to Laravel, including user authentication, role hierarchy, and access control rules.

## Current Status
- ✅ User model migrated to Eloquent
- ✅ User authentication support in Eloquent User model
- ✅ **Phase 1 Complete:** Analysis & planning done
- 📄 **Analysis Document:** [`reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md`](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md)
- ⏸️ **Waiting on:** Service integration (Task 13) to complete
- **Progress:** 25% (User model + Phase 1 analysis complete)

---

## Prerequisites

**THIS TASK SHOULD START AFTER:**
- ✅ Task 12 (Eloquent Models) - At least User, Role models complete
- ✅ Task 13 (Service Integration) - Services can use Eloquent repositories
- ⏸️ **Can prepare in advance:** Analysis and planning phase

---

## Task Breakdown

### Phase 1: Analysis & Planning ✅ COMPLETE

**Goal:** Understand current authentication system fully

1. **Analyze Current Authentication System** ✅
   - [x] Document all authentication providers
   - [x] Document role hierarchy structure
   - [x] Document access control rules
   - [x] Document "remember me" implementation
   - [x] Document password reset flow
   - [x] Document SSO/providers if any
   - **Output:** ✅ [Auth system analysis document](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md)

2. **Design Laravel Authentication** ✅
   - [x] Choose authentication guard configuration
   - [x] Design role/permission system (Gates vs Policies vs Spatie)
   - [x] Plan middleware for access control
   - [x] Plan remember me implementation
   - [x] Plan password reset implementation
   - **Output:** ✅ [Laravel auth design document](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md#laravel-authentication-design)

3. **Create Migration Strategy** ✅
   - [x] Plan gradual migration approach
   - [x] Plan parallel running (Symfony + Laravel)
   - [x] Plan session sharing strategy
   - [x] Plan rollback strategy
   - **Output:** ✅ [Migration strategy document](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md#migration-strategy)

### Phase 2: Core Authentication (After Service Integration)

4. **Migrate User Authentication**
   - [ ] Configure Laravel auth guards
   - [ ] Update User model for Laravel auth
   - [ ] Create authentication controllers
   - [ ] Implement login/logout
   - [ ] Implement remember me
   - [ ] Test authentication flows

5. **Migrate Password Reset**
   - [ ] Create password reset controllers
   - [ ] Implement reset token generation
   - [ ] Implement reset email sending
   - [ ] Test password reset flow

### Phase 3: Authorization System (After Authentication)

6. **Migrate Role System**
   - [ ] Convert role hierarchy to Laravel structure
   - [ ] Choose authorization approach (Gates/Policies/Spatie)
   - [ ] Implement role checks
   - [ ] Test role-based access

7. **Migrate Access Control Rules**
   - [ ] Convert access rules to Laravel middleware/policies
   - [ ] Create access control middleware
   - [ ] Test access control

8. **Migrate Authentication Middleware**
   - [ ] Create auth middleware
   - [ ] Create role middleware
   - [ ] Create access rule middleware
   - [ ] Apply to routes

---

## Analysis Tasks (Can Start Now)

### 1. Analyze Current Authentication

**Location:** `src/AppBundle/Security/` and related files

**What to Document:**
- Authentication providers
- User providers
- Firewall configuration
- Role definitions
- Access control rules
- Remember me configuration
- Password reset implementation

### 2. Analyze Authorization

**Location:** `src/AppBundle/Security/` and access control services

**What to Document:**
- Role hierarchy
- Permission system
- Access rule definitions
- AccessControlService implementation
- Access rule evaluation logic

### 3. Create Design Document

**Document Should Include:**
- Laravel auth guard configuration
- Role/permission system design
- Middleware design
- Migration approach
- Testing strategy

---

## Implementation Guide

### Laravel Authentication Setup

**Basic Guard Configuration:**
```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

### Role System Options

**Option 1: Laravel Gates**
- Simple role checks
- Good for basic role hierarchy
- Built-in Laravel feature

**Option 2: Laravel Policies**
- Object-based permissions
- Good for complex permissions
- Built-in Laravel feature

**Option 3: Spatie Permission Package**
- Full-featured role/permission system
- More features than needed?
- External dependency

**Recommendation:** Start with Gates, upgrade to Policies if needed

### Access Control Rules to Middleware

**Before (Symfony):**
```php
// Access rule check in service
if (!$accessControlService->checkAccess($user, $resource)) {
    throw new AccessDeniedException();
}
```

**After (Laravel):**
```php
// Middleware
Route::middleware('can:access,resource')->group(function () {
    // Routes
});

// Or in controller
$this->authorize('access', $resource);
```

---

## Acceptance Criteria

### Phase 1 (Analysis) Complete When:
- [x] Current auth system fully documented ✅
- [x] Laravel auth design created ✅
- [x] Migration strategy documented ✅
- [x] No blockers identified ✅

**✅ Phase 1 COMPLETE - See analysis document:** [`reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md`](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md)

### Phase 2 (Authentication) Complete When:
- [ ] Users can log in via Laravel
- [ ] Users can log out via Laravel
- [ ] Remember me functionality works
- [ ] Password reset flow works
- [ ] Authentication tested thoroughly
- [ ] Parallel running with Symfony works

### Phase 3 (Authorization) Complete When:
- [ ] Role system migrated to Laravel
- [ ] Access control rules converted
- [ ] Middleware created and tested
- [ ] Authorization tested thoroughly
- [ ] All existing permissions work

---

## Testing Strategy

### Authentication Tests
- [ ] Login succeeds with valid credentials
- [ ] Login fails with invalid credentials
- [ ] Logout works
- [ ] Remember me creates persistent session
- [ ] Password reset email sent
- [ ] Password reset token works
- [ ] Password reset updates password

### Authorization Tests
- [ ] Role checks work
- [ ] Access control rules evaluated correctly
- [ ] Middleware blocks unauthorized access
- [ ] Middleware allows authorized access
- [ ] Role hierarchy respected

---

## Notes

- **Critical System:** Authentication is critical - test thoroughly
- **Parallel Running:** Must work alongside Symfony during migration
- **Session Sharing:** May need to share sessions between Symfony/Laravel
- **Gradual Migration:** Can migrate one route at a time

---

## Reference Files

### Current Implementation
- `src/AppBundle/Security/` - Symfony security configuration
- `src/AppBundle/Controller/SecurityController.php` - Login/logout
- `src/AppBundle/Controller/PasswordResetController.php` - Password reset
- `src/AppBundle/Service/AccessControlService.php` - Access control

### Laravel Examples
- Laravel authentication documentation
- Laravel authorization documentation
- Laravel middleware documentation

---

**Status:** ✅ Phase 1 Complete - Analysis & planning done. ⏸️ Waiting on Tasks 12-13 for Phase 2 implementation  
**Priority:** 🔴 High (Critical for application functionality)  
**Estimated Time:** 2-3 weeks (after prerequisites met)  
**Analysis Document:** [`reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md`](../reference/AUTHENTICATION_AUTHORIZATION_ANALYSIS.md)

