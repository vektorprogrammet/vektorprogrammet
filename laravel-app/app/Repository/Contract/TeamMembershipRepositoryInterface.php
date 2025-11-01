<?php

namespace App\Repository\Contract;

use App\Models\Department;
use App\Models\Semester;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;

/**
 * Interface for TeamMembership repository operations.
 * This interface defines the contract for team membership data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface TeamMembershipRepositoryInterface
{
    /**
     * Find team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findByTeam(Team $team): array;

    /**
     * Find team memberships by user.
     *
     * @param User $user
     * @return TeamMembership[]
     */
    public function findByUser(User $user): array;

    /**
     * Find all active team memberships.
     *
     * @return TeamMembership[]
     */
    public function findActiveTeamMemberships(): array;

    /**
     * Find active team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeam(Team $team): array;

    /**
     * Filter team memberships to exclude those not in the given semester.
     *
     * @param TeamMembership[] $teamMemberships
     * @param Semester $semester
     * @return TeamMembership[]
     */
    public function filterNotInSemester(array $teamMemberships, Semester $semester): array;

    /**
     * Find active team memberships by team and user.
     *
     * @param Team $team
     * @param User $user
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeamAndUser(Team $team, User $user): array;

    /**
     * Find inactive team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findInactiveTeamMembershipsByTeam(Team $team): array;

    /**
     * Find active team memberships by user.
     *
     * @param User $user
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByUser(User $user): array;

    /**
     * Find team memberships by user and semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return TeamMembership[]
     */
    public function findTeamMembershipsByUserAndSemester($user, Semester $semester): array;

    /**
     * Find team memberships by department.
     *
     * @param Department $department
     * @return TeamMembership[]
     */
    public function findTeamMembershipsByDepartment(Department $department): array;
}

