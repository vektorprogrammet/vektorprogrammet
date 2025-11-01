# Agent Task: Create Eloquent Models

## Objective
Create Eloquent models for remaining entities to support Eloquent repositories and enable service integration.

## Current Status
- ✅ 5 core Eloquent models created (User, Department, Semester, Application, AdmissionPeriod)
- ✅ All 32 Eloquent repositories implemented (many need models to function)
- ⏳ **45-50+ models remaining** to create
- **Progress:** 5/60+ (8%)

## Priority Groups

### Group 1: Core Business Models (HIGH PRIORITY) ⭐
**These models are required by frequently-used repositories:**

1. **Article** - Required by ArticleRepository
2. **Team** - Required by TeamRepository
3. **Interview** - Required by InterviewRepository
4. **Survey** - Required by SurveyRepository
5. **Receipt** - Required by ReceiptRepository
6. **School** - Required by SchoolRepository
7. **FieldOfStudy** - Required by FieldOfStudyRepository
8. **Feedback** - Required by FeedbackRepository
9. **Role** - Required by RoleRepository
10. **Signature** - Required by SignatureRepository

### Group 2: Supporting Models (MEDIUM PRIORITY)
**These models support core business logic:**

11. **AssistantHistory** - Required by AssistantHistoryRepository
12. **ExecutiveBoard** - Required by ExecutiveBoardRepository
13. **ExecutiveBoardMembership** - Required by ExecutiveBoardMembershipRepository
14. **TeamMembership** - Required by TeamMembershipRepository
15. **TeamApplication** - Required by TeamApplicationRepository
16. **AdmissionSubscriber** - Required by AdmissionSubscriberRepository
17. **AdmissionNotification** - Required by AdmissionNotificationRepository
18. **SurveyTaken** - Required by SurveyTakenRepository
19. **SurveyNotification** - Required by SurveyNotificationRepository
20. **StaticContent** - Required by StaticContentRepository
21. **SocialEvent** - Required by SocialEventRepository
22. **PasswordReset** - Required by PasswordResetRepository
23. **Admission** - Required by AdmissionRepository
24. **SchoolCapacity** - Required by SchoolCapacityRepository
25. **ChangeLogItem** - Required by ChangeLogItemRepository
26. **AccessRule** - Required by AccessRuleRepository
27. **UnhandledAccessRule** - Required by UnhandledAccessRuleRepository

### Group 3: Additional Models (LOWER PRIORITY)
- CertificateRequest
- InfoMeeting
- Position
- Sponsor
- SurveyAnswer
- UserGroup
- And others as discovered

---

## Task Assignment

**This task can be split across multiple agents working in parallel:**

- **Agent A:** Group 1 (Core Business Models) - ~10 models
- **Agent B:** Group 2 (Supporting Models) - ~17 models  
- **Agent C:** Group 3 (Additional Models) - ~15-20 models

Each agent should work on models in priority order within their group.

---

## Implementation Guide

### Step 1: Find Doctrine Entity

Locate the Doctrine entity in `src/AppBundle/Entity/`:
- Example: `src/AppBundle/Entity/Article.php`
- Study the entity structure, relationships, and properties

### Step 2: Create Eloquent Model

Create model in `laravel-app/app/Models/`:
- Example: `laravel-app/app/Models/Article.php`
- Follow patterns from existing models (User, Department, etc.)

### Step 3: Reference Existing Models

Study these completed models as patterns:
- `laravel-app/app/Models/User.php` - Complex relationships, authentication
- `laravel-app/app/Models/Department.php` - Simple structure
- `laravel-app/app/Models/Application.php` - Complex relationships
- `laravel-app/app/Models/Semester.php` - Date handling
- `laravel-app/app/Models/AdmissionPeriod.php` - Relationships

---

## Conversion Pattern

### Doctrine Entity Example
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
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\ManyToMany(targetEntity="Department")
     * @ORM\JoinTable(name="articles_departments")
     */
    protected $departments;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;
}
```

### Eloquent Model Equivalent
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * Article Model (converted from Doctrine)
 *
 * @property int $id
 * @property string $title
 * @property int|null $author_id
 * @property Carbon|null $created
 * @property Carbon|null $updated_at
 */
class Article extends Model
{
    protected $table = 'article';

    protected $fillable = [
        'title',
        'slug',
        'article',
        'image_large',
        'image_small',
        'author_id',
        'sticky',
        'published',
    ];

    protected $casts = [
        'sticky' => 'boolean',
        'published' => 'boolean',
        'created' => 'datetime',
    ];

    /**
     * Get the author that owns the article.
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the departments for the article.
     *
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'articles_departments',
            'article_id',
            'department_id'
        );
    }
}
```

---

## Relationship Mapping Guide

| Doctrine | Eloquent | Example |
|----------|----------|---------|
| `@ORM\ManyToOne` | `belongsTo()` | `$article->belongsTo(User::class, 'author_id')` |
| `@ORM\OneToMany` | `hasMany()` | `$user->hasMany(Article::class, 'author_id')` |
| `@ORM\ManyToMany` | `belongsToMany()` | `$article->belongsToMany(Department::class, 'articles_departments')` |
| `@ORM\OneToOne` | `hasOne()` / `belongsTo()` | `$user->hasOne(Profile::class)` |

### Column Naming
- **Doctrine:** camelCase properties (`authorId`)
- **Eloquent:** snake_case columns (`author_id`)
- **Foreign Keys:** Update foreign key names in relationships

---

## Required Elements for Each Model

### 1. Class Structure
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Add relationship imports as needed

class ModelName extends Model
{
    // Implementation
}
```

### 2. Table Name
```php
protected $table = 'table_name'; // If different from Laravel convention
```

### 3. Fillable Properties
```php
protected $fillable = [
    'column1',
    'column2',
    // All columns that can be mass-assigned
];
```

### 4. Type Casts
```php
protected $casts = [
    'boolean_field' => 'boolean',
    'date_field' => 'datetime',
    'json_field' => 'array',
];
```

### 5. Relationships
```php
/**
 * Relationship method with return type and PHPDoc.
 *
 * @return BelongsTo|HasMany|BelongsToMany|HasOne
 */
public function relationshipName(): RelationshipType
{
    return $this->relationshipMethod(RelatedModel::class, 'foreign_key');
}
```

### 6. Default Attributes (if needed)
```php
protected $attributes = [
    'default_field' => 'default_value',
];
```

### 7. Timestamps Configuration (if different)
```php
public $timestamps = true; // or false
const CREATED_AT = 'created'; // Custom created_at column
const UPDATED_AT = 'updated'; // Custom updated_at column
```

---

## Special Cases

### User Authentication (Already Done ✅)
- Extends `Illuminate\Foundation\Auth\User`
- Uses `Notifiable` trait
- Includes authentication-specific fields

### Pivot Tables
For many-to-many relationships, Laravel handles pivot tables automatically:
```php
// Doctrine join table becomes:
public function departments(): BelongsToMany
{
    return $this->belongsToMany(
        Department::class,
        'articles_departments',  // pivot table name
        'article_id',           // foreign key of current model
        'department_id'         // foreign key of related model
    );
}
```

### Polymorphic Relationships
If Doctrine entity uses polymorphic relationships, convert using:
- `morphTo()` / `morphMany()` / `morphToMany()`

### Soft Deletes
If Doctrine entity has soft delete behavior:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}
```

---

## Acceptance Criteria

For each model, verify:

- [ ] Model extends `Illuminate\Database\Eloquent\Model` (or `User` for User model)
- [ ] Table name correctly specified (if different from convention)
- [ ] All properties from Doctrine entity are represented
- [ ] All relationships mapped correctly (belongsTo, hasMany, belongsToMany, etc.)
- [ ] Fillable/guarded arrays configured correctly
- [ ] Type casts defined for booleans, dates, arrays, JSON, etc.
- [ ] Timestamps configuration matches Doctrine entity
- [ ] Primary key configuration correct (if not 'id')
- [ ] Foreign keys properly named in relationships
- [ ] PHPDoc comments complete (class and relationship methods)
- [ ] No syntax errors
- [ ] PHPStan/static analysis passes (if configured)
- [ ] Repository can use the model successfully

---

## Testing

### Manual Testing Checklist

1. **Create a model instance:**
   ```php
   $model = new Article();
   $model->title = 'Test';
   $model->save();
   ```

2. **Test relationships:**
   ```php
   $article = Article::find(1);
   $author = $article->author; // Should return User
   $departments = $article->departments; // Should return collection
   ```

3. **Test repository integration:**
   - Ensure repository methods can use the model
   - Verify queries work correctly

### Repository Integration Test
```php
// In repository test
$article = Article::create(['title' => 'Test']);
$found = $repository->findById($article->id);
$this->assertNotNull($found);
```

---

## Progress Tracking

### Group 1: Core Business Models
- [ ] Article
- [ ] Team
- [ ] Interview
- [ ] Survey
- [ ] Receipt
- [ ] School
- [ ] FieldOfStudy
- [ ] Feedback
- [ ] Role
- [ ] Signature

### Group 2: Supporting Models
- [ ] AssistantHistory
- [ ] ExecutiveBoard
- [ ] ExecutiveBoardMembership
- [ ] TeamMembership
- [ ] TeamApplication
- [ ] AdmissionSubscriber
- [ ] AdmissionNotification
- [ ] SurveyTaken
- [ ] SurveyNotification
- [ ] StaticContent
- [ ] SocialEvent
- [ ] PasswordReset
- [ ] Admission
- [ ] SchoolCapacity
- [ ] ChangeLogItem
- [ ] AccessRule
- [ ] UnhandledAccessRule

### Group 3: Additional Models
- [ ] (Models discovered during implementation)

---

## Notes

- **Start with Group 1** - These are highest priority and most needed
- **Work incrementally** - Create one model, test it, commit, then move to next
- **Follow existing patterns** - Use User, Department, Application models as references
- **Ask questions** - If relationship is unclear, ask before implementing
- **Test relationships** - Verify all relationships work correctly
- **Check repository usage** - Ensure the model works with existing Eloquent repositories

---

## Reference Files

### Existing Models (Study These)
- `laravel-app/app/Models/User.php` - Complex example with authentication
- `laravel-app/app/Models/Department.php` - Simple example
- `laravel-app/app/Models/Application.php` - Complex relationships
- `laravel-app/app/Models/Semester.php` - Date handling
- `laravel-app/app/Models/AdmissionPeriod.php` - Relationships

### Doctrine Entities (Reference)
- `src/AppBundle/Entity/Article.php`
- `src/AppBundle/Entity/Team.php`
- (All entities in `src/AppBundle/Entity/`)

### Repositories (Check Usage)
- Check `laravel-app/app/Repository/Eloquent/` to see how repositories will use models
- Example: `laravel-app/app/Repository/Eloquent/ArticleRepository.php`

---

## Coding Standards

**MUST follow:** `docs/tasks/CODING_STANDARDS.md`

Key requirements:
- ✅ Type hints on all parameters
- ✅ Return types on all methods (including `void`)
- ✅ Complete PHPDoc comments
- ✅ Relationship methods have proper return types

---

## Estimated Time

- **Per Model:** 30-60 minutes (depending on complexity)
- **Group 1 (10 models):** 5-10 hours
- **Group 2 (17 models):** 8-17 hours
- **Group 3 (variable):** As discovered

**Total Estimated:** 15-30 hours for all models

---

## Questions or Issues?

If you encounter:
- **Unclear relationships:** Ask for clarification
- **Missing Doctrine entity:** Search codebase or ask
- **Complex relationships:** Check existing models for patterns
- **Special cases:** Review User model for authentication patterns

---

**Status:** ⏳ Ready for Delegation  
**Priority:** 🔴 High (Blocking service integration)  
**Can Work in Parallel:** ✅ Yes (split across agents by group)

