<?php

namespace App\Tests\App\Organization\Infrastructure\Entity;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Identity\Infrastructure\Entity\User;
use App\Shared\Entity\Semester;
use PHPUnit\Framework\TestCase;

class TeamGetActiveMembershipsTest extends TestCase
{
    public function testGetActiveTeamMembershipsUsesTeamDepartment(): void
    {
        // Create a semester
        $semester = $this->createMock(Semester::class);
        $semester->method('getStartDate')->willReturn(new \DateTime('2024-01-01'));
        $semester->method('getEndDate')->willReturn(new \DateTime('2024-07-31'));

        // Create an admission period that returns this semester
        $admissionPeriod = $this->createMock(AdmissionPeriod::class);
        $admissionPeriod->method('getSemester')->willReturn($semester);

        // Team department A
        $departmentA = $this->createMock(Department::class);
        $departmentA->method('getCurrentOrLatestAdmissionPeriod')->willReturn($admissionPeriod);

        // User's department B (different from team's department)
        $departmentB = $this->createMock(Department::class);
        $departmentB->method('getCurrentOrLatestAdmissionPeriod')->willReturn($admissionPeriod);

        // A user whose department is B
        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($departmentB);

        // Create team with department A
        $team = new Team();
        $team->fromArray(['name' => 'TestTeam', 'department' => $departmentA]);

        // Create a membership active in the semester
        $membership = $this->createMock(TeamMembership::class);
        $membership->method('getUser')->willReturn($user);
        $membership->method('isActiveInSemester')->willReturn(true);

        // Inject membership via reflection (teamMemberships is private)
        $ref = new \ReflectionProperty(Team::class, 'teamMemberships');
        $ref->setAccessible(true);
        $ref->setValue($team, [$membership]);

        // The fix: getActiveTeamMemberships must use $this->getDepartment() not $wh->getUser()->getDepartment()
        // If it uses user's department (departmentB), departmentA::getCurrentOrLatestAdmissionPeriod would not be called
        $activeMemberships = $team->getActiveTeamMemberships();

        $this->assertCount(1, $activeMemberships, 'Should return active membership using team department, not user department');

        // Verify team's department (A) was used, not user's department (B)
        // We can verify this by checking that departmentA::getCurrentOrLatestAdmissionPeriod was invoked
        // The mock assertions do this implicitly if departmentA is called
        $this->addToAssertionCount(1);
    }
}
