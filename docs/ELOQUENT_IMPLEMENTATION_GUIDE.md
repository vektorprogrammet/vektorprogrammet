# Eloquent ORM Implementation Guide

## Decision: ✅ Replace Doctrine with Eloquent

**Why:** Better Laravel integration, performance, and long-term maintainability.

---

## Migration Approach

### Step 1: Update Repository Interface Namespaces

Repository interfaces currently reference `App\Entity\User`. Need to update to `App\Models\User`.

**Example:**
```php
// Before
use App\Entity\User;

interface UserRepositoryInterface {
    public function findById(int $id): ?User;
}

// After
use App\Models\User;

interface UserRepositoryInterface {
    public function findById(int $id): ?User;
}
```

### Step 2: Create Eloquent Models

Convert Doctrine entities to Eloquent models, mapping:
- Doctrine annotations → Eloquent properties
- Relationships → Eloquent relationship methods
- Column names → Snake case (camelCase → snake_case)

### Step 3: Implement Eloquent Repositories

Create repository implementations using Eloquent Query Builder.

### Step 4: Wire Up Repositories

Create `RepositoryServiceProvider` to bind interfaces to Eloquent implementations.

---

## Next Steps

1. Start with **User** model (most critical)
2. Create sample Eloquent repository implementation
3. Wire up in service provider
4. Test with a simple service

Would you like me to start creating the Eloquent models now?

