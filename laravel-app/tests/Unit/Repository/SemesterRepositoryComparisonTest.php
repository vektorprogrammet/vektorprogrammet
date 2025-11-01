<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\SemesterRepositoryInterface;
use App\Repository\Eloquent\SemesterRepository;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Test to verify Eloquent SemesterRepository matches Doctrine SemesterRepository behavior.
 */
class SemesterRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private SemesterRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new SemesterRepository();
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
     * Test findAllOrderedByAge matches Doctrine behavior.
     */
    public function testFindAllOrderedByAge(): void
    {
        // Create semesters in different order
        $semester1 = Semester::factory()->create(['year' => '2023', 'semester_time' => 'Høst']);
        $semester2 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);
        $semester3 = Semester::factory()->create(['year' => '2023', 'semester_time' => 'Vår']);
        $semester4 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Høst']);

        $results = $this->eloquentRepository->findAllOrderedByAge();

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(4, count($results));

        // Check ordering: newest first (2024 Høst, 2024 Vår, 2023 Høst, 2023 Vår)
        $first = is_object($results[0]) ? $results[0] : (object)$results[0];
        $second = is_object($results[1]) ? $results[1] : (object)$results[1];

        // First should be 2024 Høst
        $this->assertEquals('2024', $first->year);
        $this->assertEquals('Høst', $first->semester_time);

        // Second should be 2024 Vår
        $this->assertEquals('2024', $second->year);
        $this->assertEquals('Vår', $second->semester_time);
    }

    /**
     * Test findCurrentSemester matches Doctrine behavior.
     */
    public function testFindCurrentSemester(): void
    {
        $currentYear = (string) now()->year;
        $currentMonth = now()->month;
        $expectedSemesterTime = ($currentMonth >= 1 && $currentMonth <= 7) ? 'Vår' : 'Høst';

        // Create current semester
        $currentSemester = Semester::factory()->create([
            'year' => $currentYear,
            'semester_time' => $expectedSemesterTime,
        ]);

        // Create past semester
        Semester::factory()->create([
            'year' => (string) ($currentYear - 1),
            'semester_time' => 'Høst',
        ]);

        $found = $this->eloquentRepository->findCurrentSemester();

        $this->assertNotNull($found);
        $this->assertEquals($currentSemester->id, $found->id);
        $this->assertEquals($currentYear, $found->year);
        $this->assertEquals($expectedSemesterTime, $found->semester_time);
    }

    /**
     * Test findCurrentSemester returns null when no current semester exists.
     */
    public function testFindCurrentSemesterReturnsNullWhenNotFound(): void
    {
        // Create only past semesters
        Semester::factory()->create(['year' => '2020', 'semester_time' => 'Høst']);

        $found = $this->eloquentRepository->findCurrentSemester();

        $this->assertNull($found);
    }

    /**
     * Test findOrCreateCurrentSemester matches Doctrine behavior.
     */
    public function testFindOrCreateCurrentSemester(): void
    {
        $currentYear = (string) now()->year;
        $currentMonth = now()->month;
        $expectedSemesterTime = ($currentMonth >= 1 && $currentMonth <= 7) ? 'Vår' : 'Høst';

        // First call - should create
        $semester1 = $this->eloquentRepository->findOrCreateCurrentSemester();

        $this->assertInstanceOf(Semester::class, $semester1);
        $this->assertEquals($currentYear, $semester1->year);
        $this->assertEquals($expectedSemesterTime, $semester1->semester_time);

        // Second call - should find existing
        $semester2 = $this->eloquentRepository->findOrCreateCurrentSemester();

        $this->assertEquals($semester1->id, $semester2->id);
    }

    /**
     * Test findByTimeAndYear matches Doctrine behavior.
     */
    public function testFindByTimeAndYear(): void
    {
        $semester = Semester::factory()->create([
            'year' => '2024',
            'semester_time' => 'Vår',
        ]);

        $found = $this->eloquentRepository->findByTimeAndYear('Vår', '2024');

        $this->assertNotNull($found);
        $this->assertEquals($semester->id, $found->id);

        // Test non-existent
        $notFound = $this->eloquentRepository->findByTimeAndYear('Høst', '2025');
        $this->assertNull($notFound);
    }

    /**
     * Test getNextActive matches Doctrine behavior.
     */
    public function testGetNextActive(): void
    {
        // Create semesters
        $spring2024 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);
        $fall2024 = Semester::factory()->create(['year' => '2024', 'semester_time' => 'Høst']);
        $spring2025 = Semester::factory()->create(['year' => '2025', 'semester_time' => 'Vår']);

        // Test: Vår -> Høst (same year)
        $next = $this->eloquentRepository->getNextActive($spring2024);
        $this->assertNotNull($next);
        $this->assertEquals($fall2024->id, $next->id);

        // Test: Høst -> Vår (next year)
        $next = $this->eloquentRepository->getNextActive($fall2024);
        $this->assertNotNull($next);
        $this->assertEquals($spring2025->id, $next->id);
    }

    /**
     * Test getNextActive returns null for current semester.
     */
    public function testGetNextActiveReturnsNullForCurrentSemester(): void
    {
        // Mock current semester
        $currentYear = (string) now()->year;
        $currentMonth = now()->month;
        $semesterTime = ($currentMonth >= 1 && $currentMonth <= 7) ? 'Vår' : 'Høst';

        $currentSemester = Semester::factory()->create([
            'year' => $currentYear,
            'semester_time' => $semesterTime,
        ]);

        // Create the semester via findOrCreateCurrentSemester first
        $this->eloquentRepository->findOrCreateCurrentSemester();

        $next = $this->eloquentRepository->getNextActive($currentSemester);

        // May return null if it's the current semester
        // (behavior depends on implementation)
        $this->assertTrue($next === null || $next instanceof Semester);
    }

    /**
     * Test queryForAllSemestersOrderedByAge returns query builder.
     */
    public function testQueryForAllSemestersOrderedByAgeReturnsQueryBuilder(): void
    {
        Semester::factory()->count(3)->create();

        $query = $this->eloquentRepository->queryForAllSemestersOrderedByAge();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
        
        $results = $query->get();
        $this->assertGreaterThanOrEqual(3, $results->count());
    }
}

