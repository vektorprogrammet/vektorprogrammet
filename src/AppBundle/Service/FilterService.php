<?php

namespace AppBundle\Service;

use AppBundle\Entity\WorkHistory;
use AppBundle\Entity\Team;

class FilterService
{

    /**
     * Keeps only workhistories from $team
     *
     * @param WorkHistory[] $workHistories
     * @param Team $team
     *
     * @return WorkHistory[]
     */
    public function filterWorkHistoriesByTeam($workHistories, $team)
    {
        $filtered = [];
        foreach ($workHistories as $workHistory) {
            if ($workHistory->getTeam() === $team) {
                array_push($filtered, $workHistory);
            }
        }
        return $filtered;
    }
}
