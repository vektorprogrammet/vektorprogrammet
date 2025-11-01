# Agent Task: Convert Doctrine Migrations to Laravel Migrations

## Objective
Convert all Doctrine migrations to Laravel migrations, enabling Laravel to manage database schema independently from Symfony.

## Current Status
- ✅ Doctrine migrations exist (71 migrations)
- ✅ Database schema documented
- ⏸️ **Can start analysis now** (doesn't depend on other tasks)
- **Progress:** 0%

---

## Prerequisites

**THIS TASK CAN START IMMEDIATELY:**
- ✅ No dependencies on other tasks
- ✅ Can work in parallel with Tasks 12-13
- ✅ Analysis phase can start now

---

## Task Breakdown

### Phase 1: Analysis (Can Start Now) ⭐

1. **Audit Existing Migrations**
   - [ ] List all 71 Doctrine migrations
   - [ ] Document migration order/dependencies
   - [ ] Identify complex migrations
   - [ ] Identify data migrations vs schema migrations
   - [ ] Document any manual migration steps
   - **Output:** Migration audit document

2. **Analyze Database Schema**
   - [ ] Document current database structure
   - [ ] Document table relationships
   - [ ] Document indexes
   - [ ] Document constraints
   - [ ] Document custom SQL if any
   - **Output:** Schema analysis document

3. **Plan Conversion Strategy**
   - [ ] Plan migration order
   - [ ] Plan Laravel migration structure
   - [ ] Plan for data migrations
   - [ ] Plan for rollback scenarios
   - **Output:** Conversion strategy document

### Phase 2: Conversion (Can Start After Analysis)

4. **Convert Schema Migrations**
   - [ ] Convert each Doctrine migration to Laravel
   - [ ] Test each migration independently
   - [ ] Ensure rollback works
   - [ ] Verify schema matches

5. **Handle Data Migrations**
   - [ ] Convert data migrations to seeders
   - [ ] Or create separate data migration files
   - [ ] Test data migration
   - [ ] Verify data integrity

6. **Test Migration Suite**
   - [ ] Test fresh database migration
   - [ ] Test migration from existing database
   - [ ] Test rollback scenarios
   - [ ] Test migration order

---

## Analysis Tasks (Start Now)

### 1. Audit Doctrine Migrations

**Location:** `app/DoctrineMigrations/` or similar

**What to Document:**
- Total number of migrations
- Migration file names and versions
- Migration dependencies
- Complex migrations (those that need special attention)
- Data migrations vs schema migrations

**Command to List Migrations:**
```bash
# In Symfony project
php bin/console doctrine:migrations:status
php bin/console doctrine:migrations:list
```

### 2. Analyze Migration Content

**For Each Migration:**
- Document what it does (create table, alter table, add column, etc.)
- Document dependencies (which migrations must run first)
- Document complexity (simple vs complex)
- Note any manual steps or special cases

### 3. Categorize Migrations

**Categories:**
- **Schema Migrations:** Create/alter tables, columns, indexes
- **Data Migrations:** Insert/update data, seed initial data
- **Complex Migrations:** Multiple operations, custom SQL
- **Simple Migrations:** Single table/column operation

---

## Conversion Pattern

### Doctrine Migration Example

```php
// Doctrine Migration
public function up(Schema $schema): void
{
    $this->addSql('CREATE TABLE article (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        author_id INTEGER,
        created DATETIME NOT NULL,
        FOREIGN KEY(author_id) REFERENCES user(id)
    )');
    $this->addSql('CREATE INDEX idx_article_author ON article(author_id)');
}

public function down(Schema $schema): void
{
    $this->addSql('DROP TABLE article');
}
```

### Laravel Migration Equivalent

```php
// Laravel Migration
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->dateTime('created');
            $table->timestamps();
            
            $table->foreign('author_id')
                  ->references('id')
                  ->on('user')
                  ->onDelete('set null');
            
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article');
    }
};
```

---

## Conversion Guidelines

### Table Creation
- Use `Schema::create()` for new tables
- Use Laravel column types (`string`, `integer`, `dateTime`, etc.)
- Maintain original column names (no Laravel conventions)
- Keep foreign key constraints
- Keep indexes

### Column Types Mapping

| Doctrine | Laravel |
|----------|---------|
| `VARCHAR(255)` | `string()` or `string(255)` |
| `TEXT` | `text()` |
| `INTEGER` | `integer()` or `unsignedInteger()` |
| `BIGINT` | `bigInteger()` or `unsignedBigInteger()` |
| `BOOLEAN` | `boolean()` |
| `DATETIME` | `dateTime()` |
| `DATE` | `date()` |
| `DECIMAL` | `decimal(10, 2)` |

### Foreign Keys
```php
$table->foreign('user_id')
      ->references('id')
      ->on('user')
      ->onDelete('cascade'); // or 'set null', 'restrict'
```

### Indexes
```php
$table->index('email');
$table->unique('email');
$table->index(['user_id', 'created_at']); // Composite
```

### Data Migrations

**Option 1: Seeders**
```php
// database/seeders/InitialDataSeeder.php
public function run(): void
{
    DB::table('roles')->insert([
        ['name' => 'admin'],
        ['name' => 'user'],
    ]);
}
```

**Option 2: Migration with Data**
```php
public function up(): void
{
    Schema::create('roles', function (Blueprint $table) {
        // ...
    });
    
    DB::table('roles')->insert([
        ['name' => 'admin'],
        ['name' => 'user'],
    ]);
}
```

---

## Testing Strategy

### Test Each Migration
```bash
# Test migration up
php artisan migrate

# Test migration down (rollback)
php artisan migrate:rollback

# Test specific migration
php artisan migrate --path=/database/migrations/2024_01_01_create_articles.php
```

### Test Full Migration Suite
```bash
# Fresh database
php artisan migrate:fresh

# From existing database
php artisan migrate

# Verify schema matches
php artisan db:show
```

---

## Acceptance Criteria

### Phase 1 (Analysis) Complete When:
- [ ] All 71 migrations audited
- [ ] Migration dependencies documented
- [ ] Conversion strategy created
- [ ] Ready to start conversion

### Phase 2 (Conversion) Complete When:
- [ ] All migrations converted to Laravel
- [ ] All migrations tested (up and down)
- [ ] Full migration suite tested
- [ ] Schema matches Doctrine schema exactly
- [ ] Data migrations handled correctly
- [ ] Rollback works for all migrations
- [ ] Migration order verified

---

## Notes

- **Preserve Exact Schema:** Laravel migrations must create exact same schema as Doctrine
- **Migration Order:** Maintain Doctrine migration order
- **Data Integrity:** Ensure data migrations don't lose data
- **Rollback Safety:** All migrations must rollback safely
- **Incremental:** Can convert and test migrations one at a time

---

## Reference Files

### Doctrine Migrations
- `app/DoctrineMigrations/` - Original migrations
- Migration version files

### Laravel Migrations
- `laravel-app/database/migrations/` - Target location
- Laravel migration examples

---

## Estimated Time

- **Analysis Phase:** 1-2 days
- **Conversion Phase:** 1-2 weeks (depending on complexity)
- **Testing Phase:** 3-5 days

**Total:** 2-3 weeks

---

**Status:** ⏸️ Can start Phase 1 (Analysis) immediately  
**Priority:** 🟡 Medium-High (Important but not blocking controllers)  
**Can Work in Parallel:** ✅ Yes (no dependencies on other tasks)

