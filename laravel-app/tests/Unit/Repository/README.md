# Repository Comparison Tests

## Overview

These tests verify that Eloquent repository implementations behave correctly and match the documented interface contracts. They serve as unit tests for the Eloquent implementations.

## Test Files

- `EloquentDoctrineComparisonTest.php` - Base test class with comparison utilities
- `UserRepositoryComparisonTest.php` - Tests for User repository (6 test methods)
- `DepartmentRepositoryComparisonTest.php` - Tests for Department repository (5 test methods)
- `ApplicationRepositoryComparisonTest.php` - Tests for Application repository (6 test methods)

## Running Tests

```bash
cd laravel-app
php artisan test tests/Unit/Repository
```

Or run specific test files:

```bash
php artisan test tests/Unit/Repository/UserRepositoryComparisonTest
```

## What These Tests Verify

### ✅ Correct Data Types
- Methods return arrays, entities, or null as documented
- Exception types match interface specifications

### ✅ Correct Results
- Queries return expected entities
- Filters work correctly
- Sorting is applied properly

### ✅ Relationship Queries
- Complex queries with multiple relationships work
- Eager loading handled correctly
- Join queries produce expected results

### ✅ Edge Cases
- Null/empty results handled correctly
- Non-existent entities throw appropriate exceptions
- Case-insensitive lookups work
- Boundary conditions handled

## Factories Required

Tests use Laravel factories. Ensure factories exist for:
- ✅ User (updated to match User model schema)
- ✅ Department
- ✅ Semester
- ✅ FieldOfStudy
- ✅ AdmissionPeriod
- ✅ Application
- ✅ Team
- ✅ TeamMembership

## Integration Tests (Future)

For full Doctrine vs Eloquent comparison:
- See `tests/Feature/Repository/EloquentDoctrineIntegrationTest.php`
- Requires Symfony kernel bootstrap
- Requires shared test database

## Adding More Tests

To add tests for another repository:

1. Create test class extending `EloquentDoctrineComparisonTest`
2. Implement `getEloquentRepository()` method
3. Write tests for key methods
4. Focus on complex queries and edge cases

Example:

```php
class ArticleRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ArticleRepository();
    }

    protected function getEloquentRepository(): object
    {
        return $this->repository;
    }

    public function testFindLatestArticles(): void
    {
        // Test implementation
    }
}
```

