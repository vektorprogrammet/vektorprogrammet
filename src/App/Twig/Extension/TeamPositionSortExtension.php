<?php

namespace App\Twig\Extension;

use App\Shared\Contracts\TeamInterface;
use App\Entity\User;
use App\Support\FilterService;
use App\Support\Sorter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TeamPositionSortExtension extends AbstractExtension
{
    public function __construct(private readonly Sorter $sorter, private readonly FilterService $filterService)
    {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('team_position_sort', $this->teamPositionSortFilter(...)),
        ];
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
     *
     * @return User[]
     */
    public function teamPositionSortFilter($users, TeamInterface $team)
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
