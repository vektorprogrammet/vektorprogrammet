<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;

/**
 * Interface for TeamApplication repository operations.
 * This interface defines the contract for team application data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface TeamApplicationRepositoryInterface
{
    /**
     * Find team applications by team.
     *
     * @param Team $team
     * @return TeamApplication[]
     */
    public function findByTeam(Team $team): array;
}

