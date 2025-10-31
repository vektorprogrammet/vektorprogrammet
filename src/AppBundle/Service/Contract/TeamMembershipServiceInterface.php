<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\TeamMembership;

/**
 * Interface for TeamMembershipService service.
 * Defines contract for team membership operations.
 */
interface TeamMembershipServiceInterface
{
    /**
     * Update team memberships.
     *
     * @return TeamMembership[]
     */
    public function updateTeamMemberships();
}
