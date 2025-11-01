# Agent Task: Migrate from Doctrine to Eloquent ORM

## Objective
Replace Doctrine ORM with Laravel Eloquent ORM by:
1. Converting Doctrine entities to Eloquent models
2. Implementing repository interfaces with Eloquent
3. Wiring up repositories in service providers
4. Updating services to use Eloquent models

## Current Status
- ✅ 32 repository interfaces copied to Laravel
- ✅ 56 service interfaces and implementations migrated
- ✅ Services wired up in AppServiceProvider
- ✅ **5 core Eloquent models created** (User, Department, Semester, Application, AdmissionPeriod)
- ✅ **All 32 Eloquent repositories implemented** 🎉 **COMPLETE!**
- ✅ All repositories wired up in RepositoryServiceProvider

---

## Phase 1: Create Eloquent Models (Priority Order)

### High Priority Models (Core Entities)
1. **User** - Authentication & authorization core
2. **Department** - Central to application
3. **Semester** - Time-based queries
4. **Application** - Core business entity
5. **AdmissionPeriod** - Core business entity

### Medium Priority Models
6. Article
7. AssistantHistory
8. Team
9. Interview
10. Survey
11. Receipt
12. School

### Lower Priority Models
- All remaining entities (FieldOfStudy, Feedback, Role, etc.)

---

## Phase 2: Implement Eloquent Repositories

For each repository interface:
1. Create Eloquent implementation class
2. Implement all interface methods using Eloquent
3. Wire up in RepositoryServiceProvider
4. Test against interface contract

---

## Implementation Pattern

### Doctrine Entity → Eloquent Model Conversion

**Before (Doctrine):**
```php
<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;
}
```

**After (Eloquent):**
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $table = 'article';
    
    protected $fillable = ['title', 'slug', 'article', 'image_large', 'image_small', 'sticky', 'published', 'author_id'];
    
    protected $dates = ['created', 'updated'];
    
    /**
     * Get the author that owns the article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    
    /**
     * Get the departments for the article.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'articles_departments', 'article_id', 'department_id');
    }
}
```

### Repository Implementation Pattern

**Before (Doctrine):**
```php
class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    public function findLatestArticles(int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->orderBy('a.created', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
```

**After (Eloquent):**
```php
namespace App\Repository\Eloquent;

use App\Models\Article;
use App\Repository\Contract\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function findLatestArticles(int $limit = 10): array
    {
        return Article::where('published', true)
            ->orderBy('created', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
```

---

## Relationship Mapping Guide

### Doctrine → Eloquent Relationships

| Doctrine | Eloquent | Example |
|----------|----------|---------|
| `@ORM\ManyToOne` | `belongsTo()` | `$user->belongsTo(Department::class)` |
| `@ORM\OneToMany` | `hasMany()` | `$department->hasMany(User::class)` |
| `@ORM\ManyToMany` | `belongsToMany()` | `$article->belongsToMany(Department::class)` |
| `@ORM\OneToOne` | `hasOne()` / `belongsTo()` | `$user->hasOne(Profile::class)` |

### Column Naming
- Doctrine: camelCase (`authorId`)
- Eloquent: snake_case (`author_id`)
- Update foreign keys accordingly

---

## Task Breakdown

### Task 1: Create Core Eloquent Models ✅ COMPLETE
- [x] User model ✅
- [x] Department model ✅
- [x] Semester model ✅
- [x] Application model ✅
- [x] AdmissionPeriod model ✅

### Task 2: Create Repository Service Provider ✅ COMPLETE
- [x] Create `RepositoryServiceProvider` ✅
- [x] Register in `bootstrap/providers.php` ✅
- [x] Wire up repository bindings ✅

### Task 3: Implement Core Repositories ✅ COMPLETE
- [x] UserRepository (Eloquent) ✅
- [x] DepartmentRepository (Eloquent) ✅
- [x] SemesterRepository (Eloquent) ✅
- [x] ApplicationRepository (Eloquent) ✅
- [x] AdmissionPeriodRepository (Eloquent) ✅
- [x] **All 32 repositories implemented** ✅ **COMPLETE!**

### Task 4: Update Services
- [ ] Replace `EntityManagerInterface` dependencies
- [ ] Update service constructors to use Eloquent repositories
- [ ] Test services with Eloquent

---

## Acceptance Criteria

- [ ] All 60+ entities converted to Eloquent models (5/60+ core models complete)
- [x] All 32 repository interfaces implemented with Eloquent ✅ **COMPLETE!**
- [x] All repositories wired up in service provider ✅ **COMPLETE!**
- [ ] Services updated to use Eloquent models (Next step)
- [ ] Unit tests passing for repositories
- [ ] Integration tests passing
- [ ] No Doctrine dependencies remain in Laravel app

---

## Notes

- Start with User model (most critical)
- Keep Doctrine entities for reference during migration
- Test each model/repository independently
- Use Laravel migrations for schema (don't rely on Doctrine migrations)

---

## Estimated Time

- **Core Models (5):** 1-2 days
- **Remaining Models (55+):** 1-2 weeks
- **Repository Implementations (32):** 1-2 weeks
- **Service Updates:** 1 week
- **Testing:** 1 week

**Total:** 4-6 weeks

