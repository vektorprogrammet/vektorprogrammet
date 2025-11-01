# Repository Comparison Tests

## Overview

This test suite verifies that Eloquent repository implementations match the behavior of their Doctrine counterparts, ensuring data integrity during the Symfony to Laravel migration.

## Test Structure

### Unit Tests (`tests/Unit/Repository/`)

**Purpose:** Test Eloquent repository implementations independently.

**Files:**
- `EloquentDoctrineComparisonTest.php` - Base test class with comparison utilities
- `UserRepositoryComparisonTest.php` - User repository tests
- `DepartmentRepositoryComparisonTest.php` - Department repository tests
- `ApplicationRepositoryComparisonTest.php` - Application repository tests

**What They Test:**
- Eloquent repositories return correct data types
- Methods handle edge cases correctly (null, empty, exceptions)
- Complex queries produce expected results
- Relationships are correctly resolved

**Limitations:**
- Cannot directly compare with Doctrine (requires Symfony kernel)
- Tests Eloquent behavior against documented interface contracts

### Integration Tests (`tests/Feature/Repository/`)

**Purpose:** Compare results from both Doctrine and Eloquent repositories.

**Files:**
- `EloquentDoctrineIntegrationTest.php` - Template for integration tests

**What They Test:**
- Same queries return same results from both ORMs
- Data mapping is correct
- Relationship queries match

**Requirements:**
- Both Symfony and Laravel must access the same database
- Symfony kernel must be bootstrappable in test environment

## Running Tests

### Unit Tests Only (Laravel)

```bash
php artisan test tests/Unit/Repository
```

### Full Integration Tests (Requires Setup)

```bash
# Requires Symfony kernel bootstrap and shared database
php artisan test tests/Feature/Repository
```

## Test Data Setup

### Using Factories

Laravel factories should be created for all models to ensure consistent test data:

```php
// Example factory
User::factory()->create([
    'user_name' => 'testuser',
    'email' => 'test@example.com',
]);
```

### Shared Test Database

For integration tests, both Symfony and Laravel should use the same test database to ensure:
- Same test data
- Fair comparison
- Data consistency

## Comparison Strategies

### 1. Result Count Comparison

```php
$doctrineResults = $doctrineRepo->findAll();
$eloquentResults = $eloquentRepo->findAll();

$this->assertCount(count($doctrineResults), $eloquentResults);
```

### 2. ID-Based Comparison

```php
$doctrineIds = array_map(fn($e) => $e->getId(), $doctrineResults);
$eloquentIds = array_map(fn($e) => $e->id, $eloquentResults);

sort($doctrineIds);
sort($eloquentIds);

$this->assertEquals($doctrineIds, $eloquentIds);
```

### 3. Attribute Comparison

```php
foreach ($attributesToCompare as $attr) {
    $this->assertEquals(
        $doctrineEntity->getAttribute($attr),
        $eloquentModel->$attr
    );
}
```

## Testing Complex Queries

### Relationships

Test queries involving multiple relationships:

```php
public function testFindUsersWithTeamMembership(): void
{
    // Setup: Create user, team, membership, department, semester
    // Query: findUsersInDepartmentWithTeamMembershipInSemester
    // Assert: Same users returned from both repositories
}
```

### Filtering and Sorting

Test filters, sorting, and pagination:

```php
public function testFindAllActiveUsersByDepartment(): void
{
    // Setup: Create active and inactive users
    // Query: findAllActiveUsersByDepartment
    // Assert: Only active users returned, in correct order
}
```

### Statistical Queries

Test count and aggregation methods:

```php
public function testNumOfApplications(): void
{
    // Setup: Create applications
    // Query: numOfApplications
    // Assert: Correct count returned
}
```

## Edge Cases to Test

### Null Handling

- Non-existent entities return null/empty
- Optional relationships handled correctly
- Default values applied

### Exception Handling

- UsernameNotFoundException for non-existent users
- NonUniqueResultException for queries expecting single results
- ModelNotFoundException for missing entities

### Case Sensitivity

- Case-insensitive lookups (email, username, city, etc.)
- Case-sensitive lookups (where applicable)

### Empty Results

- Empty arrays returned for no matches
- Null returned for single-entity queries with no match

## Adding New Repository Tests

### Step 1: Create Test Class

```php
class NewRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new NewRepository();
    }

    protected function getEloquentRepository(): object
    {
        return $this->eloquentRepository;
    }

    // Add test methods...
}
```

### Step 2: Test Key Methods

Focus on:
- Most commonly used methods
- Complex queries (joins, subqueries)
- Edge cases
- Exception scenarios

### Step 3: Document Differences

If Doctrine and Eloquent behave differently (acceptable differences), document them:

```php
/**
 * Note: Eloquent returns empty array [] for no results,
 * while Doctrine may return null in some cases.
 * This is an acceptable difference.
 */
```

## Continuous Testing

### During Migration

- Run tests after each repository implementation
- Compare results side-by-side
- Document any behavioral differences

### Post-Migration

- Keep integration tests for regression testing
- Remove Doctrine-specific tests once migration complete
- Maintain Eloquent unit tests

## Troubleshooting

### Test Failures

1. **Data Mismatch:** Check test data setup, ensure same data for both
2. **Type Differences:** Convert to comparable types (arrays, IDs)
3. **Order Differences:** Sort before comparison
4. **Relationship Loading:** Ensure relationships are loaded/eager loaded

### Performance

- Integration tests may be slower (booting both frameworks)
- Consider caching test data
- Use database transactions for isolation

## Future Enhancements

- [ ] Automated comparison test generator (scan interfaces, generate tests)
- [ ] Performance benchmarking (compare query execution time)
- [ ] Query logging comparison (verify same SQL generated)
- [ ] Data integrity checks (foreign keys, constraints)

