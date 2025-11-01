# Agent Task: Laravel Setup & Service Migration (Phase 1)

## Objective
Set up Laravel alongside Symfony and migrate the service layer (services are framework-agnostic and easiest to migrate first).

## Current Status
- ✅ Pre-migration refactoring complete (85% ready)
- ✅ All services have interfaces
- ✅ All repositories have interfaces
- ⏳ Laravel setup - Starting now

---

## Task 1: Verify Prerequisites

### 1.1 PHP Version Check
- [ ] Verify PHP 8.1+ is available on system
- [ ] Test Symfony still works on PHP 8.1+
- [ ] Note PHP version: `php -v`

### 1.2 Database Access
- [ ] Verify database connection credentials
- [ ] Test connection to existing database
- [ ] Document database name, host, credentials

---

## Task 2: Create Laravel Project Structure

### 2.1 Install Laravel
```bash
# Create Laravel project in new directory
composer create-project laravel/laravel laravel-app
cd laravel-app
```

### 2.2 Configure Laravel Environment
- [ ] Copy `.env.example` to `.env`
- [ ] Configure database connection (same as Symfony)
- [ ] Set up application key: `php artisan key:generate`
- [ ] Test Laravel installation: `php artisan serve`

### 2.3 Project Structure
```
monolith/
├── symfony/          # Existing Symfony app (keep running)
├── laravel/          # New Laravel app
├── shared/           # Shared resources (if needed)
└── docs/             # Documentation
```

---

## Task 3: Migrate Service Interfaces

### 3.1 Copy Service Interfaces
- [ ] Create `app/Contracts/` directory in Laravel
- [ ] Copy all service interfaces from `src/AppBundle/Service/Contract/`
- [ ] Update namespaces from `AppBundle\Service\Contract` to `App\Contracts`
- [ ] Keep method signatures identical

### 3.2 Example Migration
**Before (Symfony):**
```php
namespace AppBundle\Service\Contract;

interface HomeServiceInterface {
    public function getHomePageData(): array;
}
```

**After (Laravel):**
```php
namespace App\Contracts;

interface HomeServiceInterface {
    public function getHomePageData(): array;
}
```

---

## Task 4: Migrate Service Implementations

### 4.1 Copy Service Classes
- [ ] Create `app/Services/` directory
- [ ] Copy service implementations from `src/AppBundle/Service/`
- [ ] Update namespaces
- [ ] Adapt dependency injection to Laravel

### 4.2 Update Dependency Injection

**Symfony (services.yml):**
```yaml
AppBundle\Service\Contract\HomeServiceInterface:
    alias: AppBundle\Service\HomeService
```

**Laravel (AppServiceProvider):**
```php
$this->app->bind(
    \App\Contracts\HomeServiceInterface::class,
    \App\Services\HomeService::class
);
```

### 4.3 Update Repository Dependencies

**Change:**
- `EntityManagerInterface` → Use Eloquent models directly
- Repository interfaces → Inject Eloquent repositories

---

## Task 5: Register Services in Laravel

### 5.1 Update AppServiceProvider
- [ ] Bind all service interfaces to implementations
- [ ] Register repository interfaces
- [ ] Test service resolution: `app(HomeServiceInterface::class)`

### 5.2 Service Container Configuration
```php
// app/Providers/AppServiceProvider.php
public function register()
{
    // Service bindings
    $this->app->bind(HomeServiceInterface::class, HomeService::class);
    $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
    // ... all 49 services
}
```

---

## Task 6: Test Service Migration

### 6.1 Unit Tests
- [ ] Create Laravel tests for each service
- [ ] Verify services work identically
- [ ] Test dependency injection

### 6.2 Integration Tests
- [ ] Test services with actual database
- [ ] Verify data retrieval works
- [ ] Test complex workflows

---

## Task 7: Create Migration Script

### 7.1 Automated Migration Helper
- [ ] Create script to copy services automatically
- [ ] Update namespaces automatically
- [ ] Generate Laravel service provider code

---

## Acceptance Criteria

- [ ] Laravel project created and running
- [ ] All 49 service interfaces migrated
- [ ] All service implementations migrated
- [ ] Services registered in Laravel container
- [ ] Unit tests passing for services
- [ ] Integration tests passing
- [ ] Documentation updated

---

## Estimated Time

- **Task 1:** 1 hour (prerequisites)
- **Task 2:** 2-3 hours (Laravel setup)
- **Task 3:** 2 hours (copy interfaces)
- **Task 4:** 4-6 hours (copy & adapt services)
- **Task 5:** 2 hours (register services)
- **Task 6:** 4-6 hours (testing)
- **Task 7:** 2 hours (automation)

**Total:** 17-22 hours (2-3 days)

---

## Notes

- Services are framework-agnostic, making them easiest to migrate
- Can test services independently before migrating controllers
- Keep Symfony running - Laravel reads same database
- Start with simple services, move to complex ones

---

## Next Steps After This Task

1. Migrate repository interfaces and Eloquent implementations
2. Create Eloquent models from Doctrine entities
3. Begin controller migration

