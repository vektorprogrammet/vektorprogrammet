<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\TeamInterface;
use AppBundle\Entity\TeamMembershipInterface;

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
    public function filterTeamMembershipsByTeam($teamMemberships, $team);

    /**
     * Filter departments by active admission.
     *
     * @param Department[] $departments
     * @param bool $hasActiveAdmission
     * @return Department[]
     */
    public function filterDepartmentsByActiveAdmission($departments, $hasActiveAdmission);
}
