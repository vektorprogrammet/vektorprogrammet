<?php

namespace App\Contract;

use App\Models\Department;
use App\Models\TeamInterface;
use App\Models\TeamMembershipInterface;

/**
 * Interface for FilterService service.
 * Defines contract for filtering operations.
 */
interface FilterServiceInterface
{
    /**
     * Filter team memberships by team.
     *
     * @param TeamMembershipInterface[] $teamMemberships
     * @param TeamInterface $team
     * @return TeamMembershipInterface[]
     */
    public function filterTeamMembershipsByTeam(array $teamMemberships, TeamInterface $team): array;

    /**
     * Filter departments by active admission.
     *
     * @param Department[] $departments
     * @param bool $hasActiveAdmission
     * @return Department[]
     */
    public function filterDepartmentsByActiveAdmission(array $departments, bool $hasActiveAdmission): array;
}
