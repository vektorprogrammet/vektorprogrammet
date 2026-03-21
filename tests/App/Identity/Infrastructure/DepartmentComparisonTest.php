<?php

namespace App\Tests\App\Identity\Infrastructure;

use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use PHPUnit\Framework\TestCase;

/**
 * Tests that department comparisons use ID equality, not object identity.
 * Doctrine proxies are not reference-equal to entities fetched via different queries.
 */
class DepartmentComparisonTest extends TestCase
{
    public function testSchoolBelongsToDepartmentUsesIdComparison(): void
    {
        // Two different Department mock objects with same ID (simulates proxy vs. real entity)
        $department1 = $this->createMock(Department::class);
        $department1->method('getId')->willReturn(42);

        $department2 = $this->createMock(Department::class);
        $department2->method('getId')->willReturn(42); // Same ID, different object

        $school = new School();
        $ref = new \ReflectionProperty(School::class, 'departments');
        $ref->setValue($school, [$department1]);

        // Should find it as belonging to department2 even though it's a different object
        $this->assertTrue($school->belongsToDepartment($department2), 'School.belongsToDepartment must use ID comparison, not object identity');
    }

    public function testSchoolDoesNotBelongToDifferentDepartment(): void
    {
        $department1 = $this->createMock(Department::class);
        $department1->method('getId')->willReturn(42);

        $department3 = $this->createMock(Department::class);
        $department3->method('getId')->willReturn(99); // Different ID

        $school = new School();
        $ref = new \ReflectionProperty(School::class, 'departments');
        $ref->setValue($school, [$department1]);

        $this->assertFalse($school->belongsToDepartment($department3), 'School should not belong to a different department');
    }
}
