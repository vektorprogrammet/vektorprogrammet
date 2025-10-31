# Agent Coding Standards

This document defines mandatory coding standards for all agents working on this project. These standards ensure code quality, consistency, and ease of migration to Laravel.

## ⚠️ Mandatory Requirements

All code written or modified by agents **MUST** follow these standards. Code reviews will check for compliance.

---

## 1. Type Hints on All Parameters

### Rule
**Every function/method parameter MUST have a type hint.**

### Examples

#### ✅ CORRECT
```php
public function findUserById(int $id): User;
public function checkAccess(string $resource, User $user = null): bool;
public function createApplication(Application $application, Department $department): void;
```

#### ❌ INCORRECT
```php
// Missing type hints
public function findUserById($id): User;
public function checkAccess($resources, $user = null): bool;
public function createApplication($application, $department): void;
```

### Allowed Types
- **Primitive types:** `int`, `string`, `float`, `bool`, `array`
- **Class names:** `User`, `Application`, `Department` (fully qualified if needed)
- **Nullable types:** `?User`, `?string`, `?array`
- **Union types:** PHP 8.0+ (`string|int`), but prefer nullable where appropriate

### Special Cases

**Arrays with specific types:**
```php
// Use PHPDoc for array element types
/**
 * @param User[] $users
 * @param string[] $emails
 */
public function processUsers(array $users, array $emails): void;
```

**Mixed types (avoid if possible):**
```php
// If truly needed, document with PHPDoc
/**
 * @param mixed $data
 */
public function handleData($data): void;
```

---

## 2. Return Types on All Methods

### Rule
**Every function/method MUST have an explicit return type declaration.**

### Required Return Types

1. **Methods that return a value:**
   ```php
   public function getUser(): User;
   public function getCount(): int;
   public function getAll(): array;
   public function findById(int $id): ?User;
   ```

2. **Methods that return nothing (void):**
   ```php
   public function save(): void;
   public function delete(): void;
   public function sendEmail(): void;
   public function changeMode(): void;
   ```

3. **Nullable return types:**
   ```php
   public function findUser(int $id): ?User;  // Returns User or null
   public function getEmail(): ?string;        // Returns string or null
   ```

### Examples

#### ✅ CORRECT
```php
// Has return type
public function getApplicationStatus(Application $application): ApplicationStatus;
public function getCurrentUser(): ?User;
public function save(Application $application): void;
public function findAll(): array;
public function isValid(): bool;
```

#### ❌ INCORRECT
```php
// Missing return types
public function getApplicationStatus(Application $application);
public function getCurrentUser();
public function save(Application $application);
public function findAll();
public function isValid();
```

### Common Return Types

- **Void:** `void` - Method performs action, returns nothing
- **Primitives:** `int`, `string`, `bool`, `float`, `array`
- **Classes:** `User`, `Application`, `Department`
- **Nullable:** `?User`, `?string`, `?array`
- **Collections:** `array` (use PHPDoc for element types: `@return User[]`)

---

## 3. Interface Requirements

### Rule
**When creating interfaces, ALL methods MUST have:**
1. ✅ Parameter type hints
2. ✅ Return type declarations
3. ✅ PHPDoc comments

### ✅ CORRECT Interface Example
```php
<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\User;
use AppBundle\Entity\Application;

/**
 * Interface for ApplicationManager service.
 * Defines contract for application management operations.
 */
interface ApplicationManagerInterface
{
    /**
     * Get application status for the given application.
     *
     * @param Application $application
     * @return ApplicationStatus
     */
    public function getApplicationStatus(Application $application): ApplicationStatus;

    /**
     * Check if user can access application.
     *
     * @param User $user
     * @param Application $application
     * @return bool
     */
    public function userCanAccess(User $user, Application $application): bool;

    /**
     * Save application to database.
     *
     * @param Application $application
     * @return void
     */
    public function save(Application $application): void;
}
```

### ❌ INCORRECT Interface Example
```php
// Missing return types, missing parameter types
interface ApplicationManagerInterface
{
    public function getApplicationStatus($application);
    public function userCanAccess($user, $application);
    public function save($application);
}
```

---

## 4. Implementation Matching

### Rule
**Implementation method signatures MUST exactly match interface signatures.**

### ✅ CORRECT
```php
// Interface
interface UserServiceInterface
{
    public function getCurrentUser(): ?User;
}

// Implementation
class UserService implements UserServiceInterface
{
    public function getCurrentUser(): ?User
    {
        // implementation
    }
}
```

### ❌ INCORRECT
```php
// Interface has return type
interface UserServiceInterface
{
    public function getCurrentUser(): ?User;
}

// Implementation missing return type (WRONG!)
class UserService implements UserServiceInterface
{
    public function getCurrentUser()  // Missing return type!
    {
        // implementation
    }
}
```

---

## 5. PHPDoc Requirements

### Rule
**All public methods MUST have PHPDoc comments with:**
- `@param` for each parameter (with type and description)
- `@return` for return value (with type and description)
- `@throws` if method throws exceptions

### ✅ CORRECT PHPDoc
```php
/**
 * Find user by ID.
 *
 * @param int $id User ID
 * @return User|null User entity or null if not found
 * @throws \Doctrine\ORM\NonUniqueResultException
 */
public function findUserById(int $id): ?User;
```

### ❌ INCORRECT PHPDoc
```php
// Missing PHPDoc
public function findUserById(int $id): ?User;

// Incomplete PHPDoc
/**
 * Find user by ID.
 */
public function findUserById(int $id): ?User;
```

### PHPDoc Format
```php
/**
 * Brief description (one line).
 *
 * Longer description if needed (multiple lines).
 *
 * @param Type $paramName Parameter description
 * @param Type|null $optionalParam Optional parameter description
 * @return Type Return value description
 * @throws ExceptionClass When this exception is thrown
 */
public function methodName(Type $paramName, ?Type $optionalParam): Type;
```

---

## 6. Nullable Types

### Rule
**Use nullable types (`?Type`) when a value can be null.**

### When to Use Nullable Types

1. **Optional parameters that can be null:**
   ```php
   public function checkAccess(string $resource, ?User $user = null): bool;
   ```

2. **Return values that can be null:**
   ```php
   public function findById(int $id): ?User;  // Returns User or null
   public function getEmail(): ?string;       // Returns string or null
   ```

3. **Properties that can be null:**
   ```php
   private ?User $currentUser = null;
   private ?string $lastError = null;
   ```

### ✅ CORRECT
```php
public function getUser(int $id): ?User;
public function findDepartment(?string $name): ?Department;
public function getCurrentUser(): ?User;
```

### ❌ INCORRECT
```php
// Should use ?User, not User|null in type hint (PHPDoc can use User|null)
public function getUser(int $id); // Missing return type, and should be ?User
public function findDepartment(string $name = null); // Use ?string instead
```

---

## 7. Void Return Type

### Rule
**Methods that don't return a value MUST have `void` return type.**

### ✅ CORRECT
```php
public function save(): void;
public function delete(): void;
public function sendEmail(User $user): void;
public function changeMode(): void;
```

### ❌ INCORRECT
```php
// Missing void return type
public function save();
public function delete();
public function sendEmail(User $user);
```

### Identifying Void Methods

Methods that are void typically:
- Perform actions (save, delete, send, update, create)
- Don't have `return` statements (or only have `return;` with no value)
- Are used for side effects

---

## 8. Array Type Hints

### Rule
**Use `array` type hint with PHPDoc for array element types.**

### ✅ CORRECT
```php
/**
 * Get all users.
 *
 * @return User[]
 */
public function getAllUsers(): array;

/**
 * Process user data.
 *
 * @param User[] $users
 * @param string[] $emails
 * @return int Number of processed users
 */
public function processUsers(array $users, array $emails): int;
```

### ❌ INCORRECT
```php
// Missing array type hint
public function getAllUsers();

// Missing PHPDoc for array element types
public function getAllUsers(): array; // What's in the array?
```

---

## 9. Checklist Before Submitting Code

Before marking work as complete, verify:

- [ ] **All parameters have type hints**
  - No `$param` without type hint
  - Use `?Type` for nullable parameters
  
- [ ] **All methods have return types**
  - Every method has `: Type` or `: void`
  - No methods without return type declarations
  
- [ ] **Interface signatures match implementations**
  - Parameter types match
  - Return types match
  
- [ ] **PHPDoc comments are complete**
  - `@param` for each parameter
  - `@return` for return value
  - `@throws` if exceptions are thrown
  
- [ ] **Nullable types used correctly**
  - `?Type` in type hints (not just PHPDoc)
  - Used for optional parameters and nullable returns
  
- [ ] **Void methods have `void` return type**
  - Methods that don't return values have `: void`
  
- [ ] **No syntax errors**
  - Run `php -l` on modified files
  - Check for linter errors

---

## 10. Examples from This Codebase

### ✅ Good Examples (Follow These)

**Repository Interface:**
```php
interface UserRepositoryInterface
{
    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserById(int $id): ?User;

    /**
     * Get all active users.
     *
     * @return User[]
     */
    public function findAllActive(): array;
}
```

**Service Interface:**
```php
interface ApplicationManagerInterface
{
    /**
     * Get application status.
     *
     * @param Application $application
     * @return ApplicationStatus
     */
    public function getApplicationStatus(Application $application): ApplicationStatus;

    /**
     * Save application.
     *
     * @param Application $application
     * @return void
     */
    public function save(Application $application): void;
}
```

### ❌ Bad Examples (Don't Do This)

```php
// Missing type hints and return types
interface BadInterface
{
    public function getUser($id);
    public function save($application);
    public function getAll();
}

// Missing return types
interface IncompleteInterface
{
    public function getUser(int $id);  // Missing return type
    public function save(Application $application);  // Should be : void
}
```

---

## 11. Quick Reference

### Parameter Types
```php
int $id                    // Required integer
?string $name             // Optional/nullable string
User $user                // Required User object
?User $user = null        // Optional User (prefer ?User)
array $items              // Array (use PHPDoc for element types)
```

### Return Types
```php
: User                    // Returns User object
: ?User                   // Returns User or null
: int                     // Returns integer
: string                  // Returns string
: bool                    // Returns boolean
: array                   // Returns array (document elements in PHPDoc)
: void                    // Returns nothing
```

### PHPDoc
```php
/**
 * Method description.
 *
 * @param Type $param Description
 * @return Type Description
 * @throws ExceptionClass When thrown
 */
```

---

## 12. Enforcement

### During Reviews

Code reviews will check:
1. ✅ All parameters have type hints
2. ✅ All methods have return types
3. ✅ Interface signatures match implementations
4. ✅ PHPDoc is complete and accurate

### Common Mistakes to Avoid

1. **Forgetting `void` return type:**
   ```php
   // Wrong
   public function save(): void;
   public function delete();  // Missing : void
   ```

2. **Missing nullable type:**
   ```php
   // Wrong
   public function getUser(): User;  // Should be ?User if can be null
   ```

3. **Missing parameter type:**
   ```php
   // Wrong
   public function findById($id): User;  // Missing int $id
   ```

4. **Interface/implementation mismatch:**
   ```php
   // Interface
   public function getUser(): ?User;
   
   // Implementation (wrong - missing return type)
   public function getUser() { }
   ```

---

## 13. Questions or Clarifications

If you're unsure about a type hint or return type:

1. **Check existing interfaces** in the codebase
2. **Check implementations** to see what they return
3. **Use PHPDoc** to clarify complex types
4. **Ask for clarification** rather than guessing

### When in Doubt

- **Use nullable types** (`?Type`) if value might be null
- **Use `void`** if method doesn't return anything
- **Use `array`** with PHPDoc for arrays of objects
- **Be explicit** - better to be verbose than unclear

---

## Summary

**Remember:**
- ✅ **Every parameter:** Must have type hint
- ✅ **Every method:** Must have return type (including `void`)
- ✅ **Every interface method:** Must match implementation exactly
- ✅ **Every public method:** Must have complete PHPDoc

**These are mandatory, not optional.**

Following these standards ensures:
- Better IDE support
- Earlier error detection
- Easier Laravel migration
- Consistent codebase
- Better maintainability

