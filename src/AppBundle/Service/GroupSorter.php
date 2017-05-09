<?php

namespace AppBundle\Service;

use AppBundle\Entity\ExecutiveBoardMember;
use AppBundle\Entity\WorkHistory;

class GroupSorter
{
    /**
     * Takes a list of workHistories from a team.
     * Returns a sorted list with "Leder" and "Nestleder" in the first and second
     * position, respectively.
     *
     * @param array $workHistories
     *
     * @return array
     */
    public function getSortedTeamMembers(array $workHistories): array
    {
        $leaders = array();
        $members = array();

       /** @var WorkHistory $workHistory */
       foreach ($workHistories as $workHistory) {
           $position = strtolower($workHistory->getPosition());
           if ($position === 'leder' || $position === 'nestleder') {
               $leaders[] = $workHistory;
           } else {
               $members[] = $workHistory;
           }
       }

        usort($members, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        usort($leaders, function (WorkHistory $a, WorkHistory $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        return array_merge($leaders, $members);
    }

    /**
     * Takes a list of ExecutiveBoardMembers from a board.
     * Returns a sorted list with "Leder" and "Nestleder" in the first and second
     * position, respectively.
     *
     * @param $executiveBoardMembers
     *
     * @return array
     */
    public function getSortedBoardMembers($executiveBoardMembers): array
    {
        $leaders = array();
        $members = array();

        /** @var ExecutiveBoardMember $executiveBoardMember */
        foreach ($executiveBoardMembers as $executiveBoardMember) {
            $position = strtolower($executiveBoardMember->getPosition());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $executiveBoardMember;
            } else {
                $members[] = $executiveBoardMember;
            }
        }

        usort($members, function (ExecutiveBoardMember $a, ExecutiveBoardMember $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        usort($leaders, function (ExecutiveBoardMember $a, ExecutiveBoardMember $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        return array_merge($leaders, $members);
    }
}
