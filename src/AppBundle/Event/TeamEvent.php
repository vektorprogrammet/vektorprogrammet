<?php

namespace AppBundle\Event;

use AppBundle\Entity\Team;
use Symfony\Component\EventDispatcher\Event;

class TeamEvent extends Event implements CrudEvent
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

    public function getObject()
    {
        return $this->getTeam();
    }

    public static function created(): string
    {
        return self::CREATED;
    }

    public static function updated(): string
    {
        return self::EDITED;
    }

    public static function deleted(): string
    {
        return self::DELETED;
    }
}
