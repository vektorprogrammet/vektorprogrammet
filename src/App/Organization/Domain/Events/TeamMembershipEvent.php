<?php

declare(strict_types=1);

namespace App\Organization\Domain\Events;

use App\Organization\Infrastructure\Entity\TeamMembership;
use Symfony\Contracts\EventDispatcher\Event;

class TeamMembershipEvent extends Event
{
    public const CREATED = 'team_membership.created';
    public const EDITED = 'team_membership.edited';
    public const DELETED = 'team_membership.deleted';
    public const EXPIRED = 'team_membership.expired';

    public function __construct(private readonly TeamMembership $teamMembership)
    {
    }

    public function getTeamMembership(): TeamMembership
    {
        return $this->teamMembership;
    }
}
