<?php

declare(strict_types=1);

namespace App\Organization\Domain\Events;

use App\Organization\Infrastructure\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    public const CREATED = 'team.created';
    public const EDITED = 'team.edited';
    public const DELETED = 'team.deleted';

    /**
     * @param string $oldTeamEmail
     */
    public function __construct(private readonly Team $team, private $oldTeamEmail)
    {
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return string
     */
    public function getOldTeamEmail()
    {
        return $this->oldTeamEmail;
    }
}
