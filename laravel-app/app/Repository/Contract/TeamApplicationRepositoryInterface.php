<?php

namespace App\Repository\Contract;

use App\Models\Team;
use App\Models\TeamApplication;

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

