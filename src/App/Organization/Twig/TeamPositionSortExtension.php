<?php

declare(strict_types=1);

namespace App\Organization\Twig;

use App\Shared\Contracts\TeamInterface;
use App\Identity\Infrastructure\Entity\User;
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
            // @phpstan-ignore argument.type (User::getActiveMemberships() PHPDoc resolved without import)
            $memberships = $this->filterService->filterTeamMembershipsByTeam($user->getActiveMemberships(), $team);
            $this->sorter->sortTeamMembershipsByPosition($memberships);
            // @phpstan-ignore argument.type (FilterService returns Shared\Contracts\TeamMembershipInterface[])
            $user->setMemberships($memberships);
        }

        $this->sorter->sortUsersByActivePositions($users);

        return $users;
    }
}
