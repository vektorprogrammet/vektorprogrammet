<?php

declare(strict_types=1);

namespace App\Organization\Domain\Events;

use App\Organization\Infrastructure\Entity\TeamInterest;
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
