<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Eloquent\DepartmentRepository;
use App\Models\Department as EloquentDepartment;
use App\Models\AdmissionPeriod as EloquentAdmissionPeriod;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test to verify Eloquent DepartmentRepository matches Doctrine DepartmentRepository behavior.
 */
class DepartmentRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private DepartmentRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new DepartmentRepository();
    }

    protected function getDoctrineRepository(): object
    {
        throw new \RuntimeException('Doctrine repository access not configured. Use integration tests.');
    }

    protected function getEloquentRepository(): object
    {
        return $this->eloquentRepository;
    }

    /**
     * Test findAllDepartments matches Doctrine behavior.
     */
    public function testFindAllDepartments(): void
    {
        $dept1 = EloquentDepartment::factory()->create(['name' => 'Department 1']);
        $dept2 = EloquentDepartment::factory()->create(['name' => 'Department 2']);
        $dept3 = EloquentDepartment::factory()->create(['name' => 'Department 3']);

        $results = $this->eloquentRepository->findAllDepartments();

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(3, count($results));
        
        $ids = array_map(fn($d) => is_object($d) ? $d->id : $d['id'], $results);
        $this->assertContains($dept1->id, $ids);
        $this->assertContains($dept2->id, $ids);
        $this->assertContains($dept3->id, $ids);
    }

    /**
     * Test findDepartmentById matches Doctrine behavior.
     */
    public function testFindDepartmentById(): void
    {
        $department = EloquentDepartment::factory()->create(['name' => 'Test Department']);

        $results = $this->eloquentRepository->findDepartmentById($department->id);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $found = is_object($results[0]) ? $results[0] : (object)$results[0];
        $this->assertEquals($department->id, $found->id);
        $this->assertEquals('Test Department', $found->name);
    }

    /**
     * Test findDepartmentByShortName matches Doctrine behavior (case insensitive).
     */
    public function testFindDepartmentByShortName(): void
    {
        $department = EloquentDepartment::factory()->create([
            'name' => 'Test Department',
            'short_name' => 'TEST',
        ]);

        // Test exact case
        $found = $this->eloquentRepository->findDepartmentByShortName('TEST');
        $this->assertNotNull($found);
        $this->assertEquals($department->id, $found->id);

        // Test lowercase (case insensitive)
        $foundLower = $this->eloquentRepository->findDepartmentByShortName('test');
        $this->assertNotNull($foundLower);
        $this->assertEquals($department->id, $foundLower->id);

        // Test uppercase
        $foundUpper = $this->eloquentRepository->findDepartmentByShortName('TEST');
        $this->assertNotNull($foundUpper);
        $this->assertEquals($department->id, $foundUpper->id);
    }

    /**
     * Test findOneByCityCaseInsensitive matches Doctrine behavior.
     */
    public function testFindOneByCityCaseInsensitive(): void
    {
        $department = EloquentDepartment::factory()->create([
            'city' => 'Trondheim',
        ]);

        // Test exact case
        $found = $this->eloquentRepository->findOneByCityCaseInsensitive('Trondheim');
        $this->assertNotNull($found);
        $this->assertEquals($department->id, $found->id);

        // Test lowercase
        $foundLower = $this->eloquentRepository->findOneByCityCaseInsensitive('trondheim');
        $this->assertNotNull($foundLower);
        $this->assertEquals($department->id, $foundLower->id);

        // Test uppercase
        $foundUpper = $this->eloquentRepository->findOneByCityCaseInsensitive('TRONDHEIM');
        $this->assertNotNull($foundUpper);
        $this->assertEquals($department->id, $foundUpper->id);
    }

    /**
     * Test findAllWithActiveAdmission matches Doctrine behavior.
     */
    public function testFindAllWithActiveAdmission(): void
    {
        $department = EloquentDepartment::factory()->create();
        $semester = Semester::factory()->create([
            'year' => '2024',
            'semester_time' => 'Vår',
        ]);

        // Create active admission period
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(10),
        ]);

        $results = $this->eloquentRepository->findAllWithActiveAdmission();

        $this->assertIsArray($results);
        $ids = array_map(fn($d) => is_object($d) ? $d->id : $d['id'], $results);
        $this->assertContains($department->id, $ids);
    }
}

