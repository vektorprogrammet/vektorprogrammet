<?php

namespace AppBundle\Service;

use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\Team;

class FilterService
{

    /**
     * Keeps only workhistories from $team
     *
     * @param TeamMembership[] $teamMemberships
     * @param Team $team
     *
     * @return TeamMembership[]
     */
    public function filterTeamMembershipsByTeam($teamMemberships, $team)
    {
        $filtered = [];
        foreach ($teamMemberships as $teamMembership) {
            if ($teamMembership->getTeam() === $team) {
                array_push($filtered, $teamMembership);
            }
        }
        return $filtered;
    }
}
