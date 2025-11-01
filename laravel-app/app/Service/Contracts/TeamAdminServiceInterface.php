<?php

namespace App\Contract;

use App\Models\Department;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;

/**
 * Interface for TeamAdminService.
 * Defines contract for team admin operations.
 */
interface TeamAdminServiceInterface
{
    /**
     * Get teams data for department.
     *
     * @param Department $department
     * @return array
     */
    public function getTeamsData(Department $department): array;

    /**
     * Get specific team data.
     *
     * @param Team $team
     * @param User $currentUser
     * @return array
     */
    public function getSpecificTeamData(Team $team, User $currentUser): array;

    /**
     * Sort team memberships by start date.
     *
     * @param TeamMembership[] $teamMemberships
     * @return TeamMembership[]
     */
    public function sortTeamMembershipsByStartDate(array $teamMemberships): array;

    /**
     * Delete team and update memberships.
     *
     * @param Team $team
     */
    public function deleteTeam(Team $team): void;
}


