<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\TeamInterface;
use AppBundle\Entity\TeamMembershipInterface;
use AppBundle\Service\Contract\FilterServiceInterface;

class FilterService implements FilterServiceInterface
{

    /**
     * Returns only memberships in $team
     *
     * @param TeamMembershipInterface[] $teamMemberships
     * @param TeamInterface $team
     *
     * @return TeamMembershipInterface[]
     */
    public function filterTeamMembershipsByTeam(array $teamMemberships, TeamInterface $team): array
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
     * Returns only departments with active admission set to $hasActiveAdmission
     *
     * @param Department[] $departments
     * @param boolean $hasActiveAdmission
     *
     * @return Department[]
     */
    public function filterDepartmentsByActiveAdmission(array $departments, bool $hasActiveAdmission): array
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
