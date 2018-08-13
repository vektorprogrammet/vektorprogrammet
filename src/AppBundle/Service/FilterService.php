<?php

namespace AppBundle\Service;

use AppBundle\Entity\TeamInterface;
use AppBundle\Entity\TeamMembershipInterface;

class FilterService
{

    /**
     * Returns only memberships in $team
     *
     * @param TeamMembershipInterface[] $teamMemberships
     * @param TeamInterface $team
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
     * Returns only departments with active admission set to $hasActiveAdmission
     *
     * @param \AppBundle\Entity\Department[] $departments
     * @param boolean $hasActiveAdmission
     *
     * @return \AppBundle\Entity\Department[]
     */
    public function filterDepartmentsByActiveAdmission($departments, $hasActiveAdmission)
    {
        $filtered = [];
        foreach ($departments as $department) {
            $currentSemester = $department->getCurrentSemester();
            $departmentHasActiveAdmission = ($currentSemester !== null && $currentSemester->hasActiveAdmission());
            if ($departmentHasActiveAdmission === $hasActiveAdmission) {
                $filtered[] = $department;
            }
        }
        return $filtered;
    }
}
