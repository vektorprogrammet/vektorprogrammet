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
}
