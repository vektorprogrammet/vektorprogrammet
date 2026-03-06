<?php

namespace App\Event;

use App\Entity\TeamInterest;
use Symfony\Contracts\EventDispatcher\Event;

class TeamInterestCreatedEvent extends Event
{
    public const NAME = 'team_interest.created';

    /**
     * TeamInterestCreatedEvent constructor.
     */
    public function __construct(private readonly TeamInterest $teamInterest)
    {
    }

    /**
     * @return TeamInterest
     */
    public function getTeamInterest()
    {
        return $this->teamInterest;
    }
}
