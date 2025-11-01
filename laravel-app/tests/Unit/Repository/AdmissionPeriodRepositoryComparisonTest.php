<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\AdmissionPeriodRepositoryInterface;
use App\Repository\Eloquent\AdmissionPeriodRepository;
use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Test to verify Eloquent AdmissionPeriodRepository matches Doctrine AdmissionPeriodRepository behavior.
 */
class AdmissionPeriodRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private AdmissionPeriodRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new AdmissionPeriodRepository();
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
     * Test findByDepartmentOrderedByTime matches Doctrine behavior.
     */
    public function testFindByDepartmentOrderedByTime(): void
    {
        $department = Department::factory()->create();

        // Create semesters and admission periods
        $semester1 = Semester::factory()->create(['year' => '2023', 'semester_time' => 'Høst']);
        $semester2 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);
        $semester3 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Høst']);

        $period1 = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester1->id,
        ]);

        $period2 = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester2->id,
        ]);

        $period3 = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester3->id,
        ]);

        $results = $this->eloquentRepository->findByDepartmentOrderedByTime($department);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(3, count($results));

        // Check ordering: newest first
        $first = is_object($results[0]) ? $results[0] : (object)$results[0];
        $this->assertEquals($period3->id, $first->id); // 2024 Høst should be first
    }

    /**
     * Test findByDepartmentAndTime matches Doctrine behavior.
     */
    public function testFindByDepartmentAndTime(): void
    {
        $department = Department::factory()->create();
        $semester = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);

        $period = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester->id,
        ]);

        // Create period for different time/year
        $otherSemester = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Høst']);
        AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $otherSemester->id,
        ]);

        $results = $this->eloquentRepository->findByDepartmentAndTime($department, 'Vår', '2024');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        
        $found = is_object($results[0]) ? $results[0] : (object)$results[0];
        $this->assertEquals($period->id, $found->id);
    }

    /**
     * Test findOneByDepartmentAndSemester matches Doctrine behavior.
     */
    public function testFindOneByDepartmentAndSemester(): void
    {
        $department = Department::factory()->create();
        $semester = Semester::factory()->create();

        $period = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester->id,
        ]);

        $found = $this->eloquentRepository->findOneByDepartmentAndSemester($department, $semester);

        $this->assertNotNull($found);
        $this->assertEquals($period->id, $found->id);
    }

    /**
     * Test findOneByDepartmentAndSemester returns null when not found.
     */
    public function testFindOneByDepartmentAndSemesterReturnsNullWhenNotFound(): void
    {
        $department = Department::factory()->create();
        $semester = Semester::factory()->create();

        $found = $this->eloquentRepository->findOneByDepartmentAndSemester($department, $semester);

        $this->assertNull($found);
    }

    /**
     * Test findOneWithActiveAdmissionByDepartment matches Doctrine behavior.
     */
    public function testFindOneWithActiveAdmissionByDepartment(): void
    {
        $department = Department::factory()->create();

        // Create active admission period
        $activePeriod = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(10),
        ]);

        // Create inactive admission period (past)
        AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(20),
        ]);

        // Create inactive admission period (future)
        AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'start_date' => now()->addDays(20),
            'end_date' => now()->addDays(30),
        ]);

        $found = $this->eloquentRepository->findOneWithActiveAdmissionByDepartment($department);

        $this->assertNotNull($found);
        $this->assertEquals($activePeriod->id, $found->id);
    }

    /**
     * Test findOneWithActiveAdmissionByDepartment with custom time.
     */
    public function testFindOneWithActiveAdmissionByDepartmentWithCustomTime(): void
    {
        $department = Department::factory()->create();
        $customTime = Carbon::now()->addDays(25);

        // Create admission period active at custom time
        $period = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'start_date' => now()->addDays(20),
            'end_date' => now()->addDays(30),
        ]);

        $found = $this->eloquentRepository->findOneWithActiveAdmissionByDepartment(
            $department,
            \DateTime::createFromFormat('Y-m-d H:i:s', $customTime->format('Y-m-d H:i:s'))
        );

        $this->assertNotNull($found);
        $this->assertEquals($period->id, $found->id);
    }

    /**
     * Test findOneWithActiveAdmissionByDepartment returns null when no active admission.
     */
    public function testFindOneWithActiveAdmissionByDepartmentReturnsNullWhenNotFound(): void
    {
        $department = Department::factory()->create();

        // Create only inactive periods
        AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(20),
        ]);

        $found = $this->eloquentRepository->findOneWithActiveAdmissionByDepartment($department);

        $this->assertNull($found);
    }
}

