<?php

namespace App\Event;

use App\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    const CREATED = 'team.created';
    const EDITED = 'team.edited';
    const DELETED = 'team.deleted';

    private $team;
    private $oldTeamEmail;

    /**
     * @param Team $team
     * @param string $oldTeamEmail
     */
    public function __construct(Team $team, $oldTeamEmail)
    {
        $this->team = $team;
        $this->oldTeamEmail = $oldTeamEmail;
    }

    /**
     * @return Team
     */
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
