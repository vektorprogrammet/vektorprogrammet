<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\TeamInterface;
use AppBundle\Entity\TeamMembershipInterface;
use AppBundle\Entity\User;
use AppBundle\Service\FilterService;
use AppBundle\Service\Sorter;

class GroupSortExtension extends \Twig_Extension
{
    private $sorter;
    private $filterService;
    public function __construct(Sorter $sorter, FilterService $filterService)
    {
        $this->sorter = $sorter;
        $this->filterService = $filterService;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('groupSort', array($this, 'groupSortFilter')),
            new \Twig_SimpleFilter('team_position_sort', array($this, 'teamPositionSortFilter')),
        );
    }

    /**
     * Takes a list of members from a group. A group is an ExecutiveBoard or
     * a team.
     * Returns a sorted list with "Leder" and "Nestleder" in the first and second
     * position, respectively.
     *
     * @param TeamMembershipInterface[] $group
     *
     * @return TeamMembershipInterface[]
     */
    public function groupSortFilter($group): array
    {
        $leaders = array();
        $members = array();

        foreach ($group as $member) {
            $position = strtolower($member->getPositionName());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $member;
            } else {
                $members[] = $member;
            }
        }

        usort($members, array($this, 'positionsCompare'));
        usort($leaders, array($this, 'positionsCompare'));

        return array_merge($leaders, $members);
    }

    private function positionsCompare(TeamMembershipInterface $a, TeamMembershipInterface $b)
    {
        return strcmp(strtolower($a->getPositionName()), strtolower($b->getPositionName()));
    }

    /**
     * Sorts a list of users by their positions in the given TeamInterface $team,
     * ordered as follows: "leder" < "nestleder" < "aaa" < "zzz" < "".
     * For users having multiple positions within $team, their list of positions
     * is also sorted in the same fashion.
     *
     * Note: Any memberships to other teams are filtered out,
     * i.e removed from the $user object!
     *
     * @param User[] $users
     * @param TeamInterface $team
     *
     * @return array
     */
    public function teamPositionSortFilter($users, TeamInterface $team): array
    {
        // Filter out any other team memberships and sort them by importance
        foreach ($users as $user) {
            $memberships = $this->filterService->filterTeamMembershipsByTeam($user->getActiveMemberships(), $team);
            $this->sorter->sortTeamMembershipsByPosition($memberships);
            $user->setMemberships($memberships);
        }

        $this->sorter->sortUsersByActivePositions($users);

        return $users;
    }
}
