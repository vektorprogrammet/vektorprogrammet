<?php

namespace App\Repository\Eloquent;

use App\Models\Team;
use App\Models\TeamApplication;
use App\Repository\Contract\TeamApplicationRepositoryInterface;

/**
 * Eloquent implementation of TeamApplicationRepositoryInterface.
 */
class TeamApplicationRepository implements TeamApplicationRepositoryInterface
{
    /**
     * Find team applications by team.
     *
     * @param Team $team
     * @return TeamApplication[]
     */
    public function findByTeam(Team $team): array
    {
        return TeamApplication::where('team_id', $team->id)
            ->get()
            ->toArray();
    }
}

