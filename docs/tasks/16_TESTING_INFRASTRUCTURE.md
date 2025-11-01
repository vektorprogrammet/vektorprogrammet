# Agent Task: Set Up Testing Infrastructure

## Objective
Establish comprehensive testing infrastructure for Laravel application, including unit tests, integration tests, and testing utilities to support migration verification.

## Current Status
- ✅ Some repository tests exist (7/32 repositories tested)
- ✅ Test factories exist (User, Department, etc.)
- ⏸️ **Can expand now** (doesn't depend on other tasks)
- **Progress:** ~20% (basic infrastructure exists)

---

## Prerequisites

**THIS TASK CAN START IMMEDIATELY:**
- ✅ Basic Laravel testing setup exists
- ✅ Can expand testing in parallel with Tasks 12-13
- ✅ No blocking dependencies

---

## Task Breakdown

### Phase 1: Expand Repository Tests (Can Start Now) ⭐

1. **Complete Repository Test Coverage**
   - [ ] Test remaining 25 repositories
   - [ ] Use existing test patterns
   - [ ] Test all repository methods
   - [ ] Test edge cases
   - **Target:** 100% repository test coverage

2. **Create Missing Factories**
   - [ ] Create factories for all models
   - [ ] Ensure factories match Eloquent models
   - [ ] Test factory relationships
   - **Target:** Factory for every model

### Phase 2: Service Tests (After Service Integration)

3. **Create Service Tests**
   - [ ] Test all services independently
   - [ ] Mock repository dependencies
   - [ ] Test service business logic
   - [ ] Test error handling
   - **Target:** Service test coverage

4. **Integration Tests**
   - [ ] Test service + repository + model integration
   - [ ] Test Doctrine vs Eloquent comparison
   - [ ] Test end-to-end workflows
   - **Target:** Critical workflow coverage

### Phase 3: Advanced Testing (Later)

5. **Controller Tests**
   - [ ] Test controllers with Laravel
   - [ ] Test authentication/authorization
   - [ ] Test request/response handling

6. **Browser Tests**
   - [ ] Set up Laravel Dusk or similar
   - [ ] Test critical user flows
   - [ ] Test authentication flows

---

## Current Test Infrastructure

### Existing Tests
- `laravel-app/tests/Unit/Repository/` - Repository tests (7/32)
- `laravel-app/tests/Feature/Repository/` - Integration tests
- `laravel-app/tests/Unit/Repository/EloquentDoctrineComparisonTest.php` - Comparison utilities

### Existing Factories
- `laravel-app/database/factories/UserFactory.php`
- `laravel-app/database/factories/DepartmentFactory.php`
- `laravel-app/database/factories/SemesterFactory.php`
- And others...

---

## Phase 1 Tasks (Can Start Now)

### 1. Expand Repository Tests

**Pattern to Follow:**
```php
<?php

namespace Tests\Unit\Repository;

use Tests\TestCase;
use App\Repository\Contract\ArticleRepositoryInterface;
use App\Models\Article;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ArticleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepositoryInterface::class);
    }

    public function test_find_by_id(): void
    {
        $article = Article::factory()->create();
        
        $found = $this->repository->findById($article->id);
        
        $this->assertNotNull($found);
        $this->assertEquals($article->id, $found->id);
    }

    // Test all repository methods...
}
```

**Test Checklist for Each Repository:**
- [ ] `findById()` - finds existing, returns null for non-existent
- [ ] `findAll()` - returns all records
- [ ] `findBy()` - filters correctly
- [ ] `save()` - creates new, updates existing
- [ ] `delete()` - deletes record
- [ ] Complex queries - test specialized methods
- [ ] Relationships - test relationship queries
- [ ] Edge cases - null values, empty results, etc.

### 2. Create Missing Factories

**For Each Model:**
```php
<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'article' => $this->faker->paragraphs(3, true),
            'author_id' => User::factory(),
            'sticky' => false,
            'published' => true,
            'created' => now(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    public function withDepartments(int $count = 1): static
    {
        return $this->afterCreating(function (Article $article) use ($count) {
            $departments = Department::factory()->count($count)->create();
            $article->departments()->attach($departments);
        });
    }
}
```

**Factory Checklist:**
- [ ] All model attributes covered
- [ ] Relationships set up correctly
- [ ] State methods for common variations
- [ ] Relationships work correctly

---

## Testing Utilities

### 1. Comparison Test Utilities

**Already Exists:** `EloquentDoctrineComparisonTest`

**Expand With:**
- Comparison helpers for different entity types
- Data comparison utilities
- Query result comparison

### 2. Test Helpers

**Create Helper Traits:**
```php
trait CreatesTestData
{
    protected function createUserWithRoles(array $roles): User
    {
        $user = User::factory()->create();
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $user->roles()->attach($role);
        }
        return $user;
    }
}
```

---

## Acceptance Criteria

### Phase 1 Complete When:
- [ ] All 32 repositories have tests
- [ ] All repositories have factories
- [ ] Test coverage for repositories > 80%
- [ ] All tests pass
- [ ] Tests run quickly (< 30 seconds for full suite)

### Phase 2 Complete When:
- [ ] Service tests created
- [ ] Integration tests created
- [ ] Test coverage > 60% overall
- [ ] Critical workflows tested

---

## Notes

- **Incremental:** Can add tests as models/services are created
- **Parallel:** Can work on tests while other tasks progress
- **Coverage Target:** Aim for 60%+ overall, 80%+ for critical code
- **Fast Tests:** Keep unit tests fast, use RefreshDatabase sparingly

---

## Reference Files

### Existing Tests
- `laravel-app/tests/Unit/Repository/UserRepositoryComparisonTest.php`
- `laravel-app/tests/Unit/Repository/DepartmentRepositoryComparisonTest.php`
- `laravel-app/tests/Feature/Repository/EloquentDoctrineIntegrationTest.php`

### Testing Documentation
- `laravel-app/tests/Unit/Repository/README.md`
- Laravel testing documentation

---

**Status:** ⏸️ Can start Phase 1 immediately  
**Priority:** 🟡 Medium (Important for quality, not blocking)  
**Can Work in Parallel:** ✅ Yes (no dependencies)

