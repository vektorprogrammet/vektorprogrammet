<?php

declare(strict_types=1);

namespace App\Support;

use App\Organization\Infrastructure\Entity\Department;
use App\Shared\Contracts\TeamInterface;
use App\Shared\Contracts\TeamMembershipInterface;

class FilterService
{
    /**
     * Returns only memberships in $team.
     *
     * @param TeamMembershipInterface[] $teamMemberships
     * @param TeamInterface             $team
     *
     * @return TeamMembershipInterface[]
     */
    public function filterTeamMembershipsByTeam($teamMemberships, $team)
    {
        $filtered = [];
        foreach ($teamMemberships as $teamMembership) {
            if ($teamMembership->getTeam() === $team) {
                $filtered[] = $teamMembership;
            }
        }

        return $filtered;
    }

    /**
     * Returns only departments with active admission set to $hasActiveAdmission.
     *
     * @param Department[] $departments
     * @param bool         $hasActiveAdmission
     *
     * @return Department[]
     */
    public function filterDepartmentsByActiveAdmission($departments, $hasActiveAdmission)
    {
        $filtered = [];
        foreach ($departments as $department) {
            $currentSemester = $department->getCurrentAdmissionPeriod();
            $departmentHasActiveAdmission = ($currentSemester !== null && $currentSemester->hasActiveAdmission());
            if ($departmentHasActiveAdmission === $hasActiveAdmission) {
                $filtered[] = $department;
            }
        }

        return $filtered;
    }
}
