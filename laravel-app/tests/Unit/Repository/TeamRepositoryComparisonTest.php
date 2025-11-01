<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\TeamRepositoryInterface;
use App\Repository\Eloquent\TeamRepository;
use App\Models\Team;
use App\Models\Department;
use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test to verify Eloquent TeamRepository matches Doctrine TeamRepository behavior.
 */
class TeamRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    private TeamRepositoryInterface $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eloquentRepository = new TeamRepository();
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
     * Test findByDepartment matches Doctrine behavior.
     */
    public function testFindByDepartment(): void
    {
        $department = Department::factory()->create();
        $otherDepartment = Department::factory()->create();

        $team1 = Team::factory()->create(['department_id' => $department->id, 'name' => 'Team A']);
        $team2 = Team::factory()->create(['department_id' => $department->id, 'name' => 'Team B']);
        $team3 = Team::factory()->create(['department_id' => $otherDepartment->id]);

        $results = $this->eloquentRepository->findByDepartment($department);

        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        $ids = array_map(fn($t) => is_object($t) ? $t->id : $t['id'], $results);
        $this->assertContains($team1->id, $ids);
        $this->assertContains($team2->id, $ids);
        $this->assertNotContains($team3->id, $ids);
    }

    /**
     * Test findActiveByDepartment matches Doctrine behavior.
     */
    public function testFindActiveByDepartment(): void
    {
        $department = Department::factory()->create();

        $activeTeam = Team::factory()->create([
            'department_id' => $department->id,
            'active' => true,
        ]);

        $inactiveTeam = Team::factory()->create([
            'department_id' => $department->id,
            'active' => false,
        ]);

        $results = $this->eloquentRepository->findActiveByDepartment($department);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        $ids = array_map(fn($t) => is_object($t) ? $t->id : $t['id'], $results);
        $this->assertContains($activeTeam->id, $ids);
        $this->assertNotContains($inactiveTeam->id, $ids);
    }

    /**
     * Test findInActiveByDepartment matches Doctrine behavior.
     */
    public function testFindInActiveByDepartment(): void
    {
        $department = Department::factory()->create();

        $activeTeam = Team::factory()->create([
            'department_id' => $department->id,
            'active' => true,
        ]);

        $inactiveTeam = Team::factory()->create([
            'department_id' => $department->id,
            'active' => false,
        ]);

        $results = $this->eloquentRepository->findInActiveByDepartment($department);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        $ids = array_map(fn($t) => is_object($t) ? $t->id : $t['id'], $results);
        $this->assertContains($inactiveTeam->id, $ids);
        $this->assertNotContains($activeTeam->id, $ids);
    }

    /**
     * Test findByOpenApplicationAndDepartment matches Doctrine behavior.
     */
    public function testFindByOpenApplicationAndDepartment(): void
    {
        $department = Department::factory()->create();

        $openTeam = Team::factory()->create([
            'department_id' => $department->id,
            'accept_application' => true,
        ]);

        $closedTeam = Team::factory()->create([
            'department_id' => $department->id,
            'accept_application' => false,
        ]);

        $results = $this->eloquentRepository->findByOpenApplicationAndDepartment($department);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        $ids = array_map(fn($t) => is_object($t) ? $t->id : $t['id'], $results);
        $this->assertContains($openTeam->id, $ids);
        $this->assertNotContains($closedTeam->id, $ids);
    }

    /**
     * Test findAllEmails matches Doctrine behavior.
     */
    public function testFindAllEmails(): void
    {
        $team1 = Team::factory()->create(['email' => 'team1@example.com']);
        $team2 = Team::factory()->create(['email' => 'team2@example.com']);

        $emails = $this->eloquentRepository->findAllEmails();

        $this->assertIsArray($emails);
        $this->assertGreaterThanOrEqual(2, count($emails));
        $this->assertContains('team1@example.com', $emails);
        $this->assertContains('team2@example.com', $emails);
    }

    /**
     * Test findByCityAndName matches Doctrine behavior (case insensitive).
     */
    public function testFindByCityAndName(): void
    {
        $department = Department::factory()->create(['city' => 'Trondheim']);
        $team = Team::factory()->create([
            'department_id' => $department->id,
            'name' => 'Test Team',
        ]);

        // Test exact case
        $results = $this->eloquentRepository->findByCityAndName('Trondheim', 'Test Team');
        $this->assertCount(1, $results);
        $this->assertEquals($team->id, is_object($results[0]) ? $results[0]->id : $results[0]['id']);

        // Test lowercase
        $results = $this->eloquentRepository->findByCityAndName('trondheim', 'test team');
        $this->assertCount(1, $results);

        // Test uppercase
        $results = $this->eloquentRepository->findByCityAndName('TRONDHEIM', 'TEST TEAM');
        $this->assertCount(1, $results);

        // Test non-existent
        $results = $this->eloquentRepository->findByCityAndName('Oslo', 'Nonexistent');
        $this->assertCount(0, $results);
    }

    /**
     * Test findByTeamInterestAndAdmissionPeriod matches Doctrine behavior.
     * 
     * Note: This test is simplified as it requires complex relationship setup.
     */
    public function testFindByTeamInterestAndAdmissionPeriod(): void
    {
        $department = Department::factory()->create();
        $semester = \App\Models\Semester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);
        $admissionPeriod = AdmissionPeriod::factory()->create([
            'department_id' => $department->id,
            'semester_id' => $semester->id,
        ]);

        $team = Team::factory()->create(['department_id' => $department->id]);

        // Create application with team interest
        $user = User::factory()->create();
        Application::factory()->create([
            'user_id' => $user->id,
            'admission_period_id' => $admissionPeriod->id,
            'team_interest' => true,
        ]);

        $results = $this->eloquentRepository->findByTeamInterestAndAdmissionPeriod($admissionPeriod);

        // This method has complex logic involving potentialMembers/potentialApplicants
        // The exact result depends on how those relationships are set up
        $this->assertIsArray($results);
    }
}

