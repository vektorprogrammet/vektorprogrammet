<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\ApplicationRepositoryInterface;
use App\Repository\Eloquent\ApplicationRepository;
use App\Models\Application as EloquentApplication;
use App\Models\User as EloquentUser;
use App\Models\AdmissionPeriod as EloquentAdmissionPeriod;
use App\Models\Department;
use App\Models\FieldOfStudy;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test to verify Eloquent ApplicationRepository matches Doctrine ApplicationRepository behavior.
 */
class ApplicationRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private ApplicationRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new ApplicationRepository();
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
     * Test findByUserInAdmissionPeriod matches Doctrine behavior.
     */
    public function testFindByUserInAdmissionPeriod(): void
    {
        $user = EloquentUser::factory()->create();
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        $application = EloquentApplication::factory()->create([
            'user_id' => $user->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        $found = $this->eloquentRepository->findByUserInAdmissionPeriod($user, $admissionPeriod);

        $this->assertNotNull($found);
        $this->assertEquals($application->id, $found->id);
        $this->assertEquals($user->id, $found->user_id);
        $this->assertEquals($admissionPeriod->id, $found->admission_period_id);
    }

    /**
     * Test findByUserInAdmissionPeriod returns null when not found.
     */
    public function testFindByUserInAdmissionPeriodReturnsNullWhenNotFound(): void
    {
        $user = EloquentUser::factory()->create();
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        $found = $this->eloquentRepository->findByUserInAdmissionPeriod($user, $admissionPeriod);

        $this->assertNull($found);
    }

    /**
     * Test findActiveByUser matches Doctrine behavior.
     */
    public function testFindActiveByUser(): void
    {
        $user = EloquentUser::factory()->create();
        $department = Department::factory()->create();
        $fieldOfStudy = FieldOfStudy::factory()->create(['department_id' => $department->id]);
        $user->update(['field_of_study_id' => $fieldOfStudy->id]);

        $semester = Semester::factory()->create([
            'year' => (string) now()->year,
            'semester_time' => now()->month >= 1 && now()->month <= 7 ? 'Vår' : 'Høst',
        ]);

        $admissionPeriod = EloquentAdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(10),
        ]);

        $application = EloquentApplication::factory()->create([
            'user_id' => $user->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        $found = $this->eloquentRepository->findActiveByUser($user);

        $this->assertNotNull($found);
        $this->assertEquals($application->id, $found->id);
    }

    /**
     * Test findEmailsByAdmissionPeriod matches Doctrine behavior.
     */
    public function testFindEmailsByAdmissionPeriod(): void
    {
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        $user1 = EloquentUser::factory()->create(['email' => 'user1@example.com']);
        $user2 = EloquentUser::factory()->create(['email' => 'user2@example.com']);
        $user3 = EloquentUser::factory()->create(['email' => 'user3@example.com']);

        EloquentApplication::factory()->create([
            'user_id' => $user1->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        EloquentApplication::factory()->create([
            'user_id' => $user2->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        // User3 with different admission period
        $otherAdmissionPeriod = EloquentAdmissionPeriod::factory()->create();
        EloquentApplication::factory()->create([
            'user_id' => $user3->id,
            'admission_period_id' => $otherAdmissionPeriod->id,
        ]);

        $emails = $this->eloquentRepository->findEmailsByAdmissionPeriod($admissionPeriod);

        $this->assertIsArray($emails);
        $this->assertCount(2, $emails);
        $this->assertContains('user1@example.com', $emails);
        $this->assertContains('user2@example.com', $emails);
        $this->assertNotContains('user3@example.com', $emails);
    }

    /**
     * Test findNewApplicants matches Doctrine behavior.
     */
    public function testFindNewApplicants(): void
    {
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        // New applicant (no interview)
        $user1 = EloquentUser::factory()->create();
        $application1 = EloquentApplication::factory()->create([
            'user_id' => $user1->id,
            'admission_period_id' => $admissionPeriod->id,
            'previous_participation' => false,
        ]);

        // Previous participant
        $user2 = EloquentUser::factory()->create();
        $application2 = EloquentApplication::factory()->create([
            'user_id' => $user2->id,
            'admission_period_id' => $admissionPeriod->id,
            'previous_participation' => true,
        ]);

        $results = $this->eloquentRepository->findNewApplicants($admissionPeriod);

        $this->assertIsArray($results);
        $ids = array_map(fn($a) => is_object($a) ? $a->id : $a['id'], $results);
        $this->assertContains($application1->id, $ids);
        $this->assertNotContains($application2->id, $ids);
    }

    /**
     * Test numOfApplications matches Doctrine behavior.
     */
    public function testNumOfApplications(): void
    {
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        EloquentApplication::factory()->count(5)->create([
            'admission_period_id' => $admissionPeriod->id,
        ]);

        $count = $this->eloquentRepository->numOfApplications($admissionPeriod);

        $this->assertEquals(5, $count);
    }

    /**
     * Test numOfGender matches Doctrine behavior.
     */
    public function testNumOfGender(): void
    {
        $admissionPeriod = EloquentAdmissionPeriod::factory()->create();

        $maleUser = EloquentUser::factory()->create(['gender' => false]);
        $femaleUser = EloquentUser::factory()->create(['gender' => true]);

        EloquentApplication::factory()->count(3)->create([
            'user_id' => $maleUser->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        EloquentApplication::factory()->count(2)->create([
            'user_id' => $femaleUser->id,
            'admission_period_id' => $admissionPeriod->id,
        ]);

        $maleCount = $this->eloquentRepository->numOfGender($admissionPeriod, false);
        $femaleCount = $this->eloquentRepository->numOfGender($admissionPeriod, true);

        $this->assertEquals(3, $maleCount);
        $this->assertEquals(2, $femaleCount);
    }
}

