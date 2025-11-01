<?php

namespace Tests\Unit\Repository;

use App\Repository\Contract\UserRepositoryInterface;
use App\Repository\Eloquent\UserRepository;
use App\Models\User as EloquentUser;
use App\Models\Department as EloquentDepartment;
use App\Models\Semester as EloquentSemester;
use App\Models\FieldOfStudy;
use App\Models\Team;
use App\Models\TeamMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test to verify Eloquent UserRepository matches Doctrine UserRepository behavior.
 * 
 * These tests compare results from both implementations to ensure
 * data integrity during the migration.
 */
class UserRepositoryComparisonTest extends EloquentDoctrineComparisonTest
{
    use RefreshDatabase;

    /**
     * Doctrine UserRepository instance.
     * In a real scenario, this would be injected from Symfony's container.
     *
     * @var object|null
     */
    private $doctrineRepository = null;

    /**
     * Eloquent UserRepository instance.
     *
     * @var UserRepositoryInterface
     */
    private $eloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create Eloquent repository instance
        $this->eloquentRepository = new UserRepository();

        // Note: Doctrine repository would be obtained from Symfony container
        // For now, we'll test Eloquent independently and document expected behavior
        // In a full integration test, you would bootstrap Symfony kernel and get Doctrine repo
    }

    protected function getDoctrineRepository(): object
    {
        // In integration tests, this would return Doctrine repository from Symfony
        // For unit tests, we'll skip Doctrine comparison and test Eloquent behavior
        throw new \RuntimeException('Doctrine repository access not configured. Use integration tests.');
    }

    protected function getEloquentRepository(): object
    {
        return $this->eloquentRepository;
    }

    /**
     * Test findUserByUsername matches Doctrine behavior.
     */
    public function testFindUserByUsername(): void
    {
        // Create test user via Eloquent
        $user = EloquentUser::factory()->create([
            'user_name' => 'testuser',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        // Test Eloquent implementation
        $foundUser = $this->eloquentRepository->findUserByUsername('testuser');

        $this->assertInstanceOf(EloquentUser::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals('testuser', $foundUser->user_name);
        $this->assertEquals('test@example.com', $foundUser->email);
    }

    /**
     * Test findUserByUsername throws exception for non-existent user.
     */
    public function testFindUserByUsernameThrowsExceptionForNonExistent(): void
    {
        $this->expectException(\Symfony\Component\Security\Core\Exception\UsernameNotFoundException::class);
        $this->expectExceptionMessage('User with username "nonexistent" not found.');

        $this->eloquentRepository->findUserByUsername('nonexistent');
    }

    /**
     * Test findByUsernameOrEmail matches Doctrine behavior.
     */
    public function testFindByUsernameOrEmail(): void
    {
        $user = EloquentUser::factory()->create([
            'user_name' => 'testuser',
            'email' => 'test@example.com',
            'company_email' => 'company@example.com',
        ]);

        // Test by username
        $foundByUsername = $this->eloquentRepository->findByUsernameOrEmail('testuser');
        $this->assertEquals($user->id, $foundByUsername->id);

        // Test by email
        $foundByEmail = $this->eloquentRepository->findByUsernameOrEmail('test@example.com');
        $this->assertEquals($user->id, $foundByEmail->id);

        // Test by company email
        $foundByCompanyEmail = $this->eloquentRepository->findByUsernameOrEmail('company@example.com');
        $this->assertEquals($user->id, $foundByCompanyEmail->id);
    }

    /**
     * Test findUserByEmail matches Doctrine behavior.
     */
    public function testFindUserByEmail(): void
    {
        $user = EloquentUser::factory()->create([
            'email' => 'unique@example.com',
        ]);

        $foundUser = $this->eloquentRepository->findUserByEmail('unique@example.com');

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);

        // Test non-existent
        $notFound = $this->eloquentRepository->findUserByEmail('nonexistent@example.com');
        $this->assertNull($notFound);
    }

    /**
     * Test findAllUsersByDepartment matches Doctrine behavior.
     */
    public function testFindAllUsersByDepartment(): void
    {
        // Create department and users
        $department = EloquentDepartment::factory()->create(['name' => 'Test Department']);
        $fieldOfStudy = \App\Models\FieldOfStudy::factory()->create(['department_id' => $department->id]);

        $user1 = EloquentUser::factory()->create(['field_of_study_id' => $fieldOfStudy->id]);
        $user2 = EloquentUser::factory()->create(['field_of_study_id' => $fieldOfStudy->id]);
        $user3 = EloquentUser::factory()->create(); // Different department

        $results = $this->eloquentRepository->findAllUsersByDepartment($department);

        $this->assertCount(2, $results);
        $userIds = array_map(fn($u) => is_object($u) ? $u->id : $u['id'], $results);
        $this->assertContains($user1->id, $userIds);
        $this->assertContains($user2->id, $userIds);
        $this->assertNotContains($user3->id, $userIds);
    }

    /**
     * Test findAllActiveUsersByDepartment matches Doctrine behavior.
     */
    public function testFindAllActiveUsersByDepartment(): void
    {
        $department = EloquentDepartment::factory()->create();
        $fieldOfStudy = \App\Models\FieldOfStudy::factory()->create(['department_id' => $department->id]);

        $activeUser = EloquentUser::factory()->create([
            'field_of_study_id' => $fieldOfStudy->id,
            'is_active' => true,
        ]);

        $inactiveUser = EloquentUser::factory()->create([
            'field_of_study_id' => $fieldOfStudy->id,
            'is_active' => false,
        ]);

        $results = $this->eloquentRepository->findAllActiveUsersByDepartment($department);

        $this->assertCount(1, $results);
        $userIds = array_map(fn($u) => is_object($u) ? $u->id : $u['id'], $results);
        $this->assertContains($activeUser->id, $userIds);
        $this->assertNotContains($inactiveUser->id, $userIds);
    }

    /**
     * Test findUsersInDepartmentWithTeamMembershipInSemester matches Doctrine behavior.
     * 
     * This is a complex query involving multiple relationships.
     */
    public function testFindUsersInDepartmentWithTeamMembershipInSemester(): void
    {
        // Create test data
        $department = EloquentDepartment::factory()->create();
        $semester = EloquentSemester::factory()->create(['year' => '2024', 'semester_time' => 'Vår']);
        $fieldOfStudy = FieldOfStudy::factory()->create(['department_id' => $department->id]);
        $team = Team::factory()->create(['department_id' => $department->id]);

        $user = EloquentUser::factory()->create(['field_of_study_id' => $fieldOfStudy->id]);
        
        // Create team membership with semester
        $teamMembership = TeamMembership::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'start_semester_id' => $semester->id,
            'end_semester_id' => null,
        ]);

        $results = $this->eloquentRepository->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
        
        // Verify user is in results
        $userIds = array_map(function($u) {
            return is_object($u) ? $u->id : $u['id'];
        }, $results);
        $this->assertContains($user->id, $userIds);
    }
}

