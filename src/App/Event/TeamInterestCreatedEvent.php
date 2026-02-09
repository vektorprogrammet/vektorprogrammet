<?php

namespace App\Event;

use App\Entity\TeamInterest;
use Symfony\Contracts\EventDispatcher\Event;

class TeamInterestCreatedEvent extends Event
{
    const NAME = 'team_interest.created';

    private $teamInterest;

    /**
     * TeamInterestCreatedEvent constructor.
     *
     * @param $teamInterest
     */
    public function __construct(TeamInterest $teamInterest)
    {
        $this->teamInterest = $teamInterest;
    }

    /**
     * @return TeamInterest
     */
    public function getTeamInterest()
    {
        return $this->teamInterest;
    }
}
