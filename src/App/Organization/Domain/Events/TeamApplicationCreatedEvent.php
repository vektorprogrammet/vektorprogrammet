<?php

declare(strict_types=1);

namespace App\Organization\Domain\Events;

use App\Organization\Infrastructure\Entity\TeamApplication;
use Symfony\Contracts\EventDispatcher\Event;

class TeamApplicationCreatedEvent extends Event
{
    public const NAME = 'team_application.created';

    /**
     * TeamApplicationCreatedEvent constructor.
     */
    public function __construct(private readonly TeamApplication $teamApplication)
    {
    }

    /**
     * @return TeamApplication
     */
    public function getTeamApplication()
    {
        return $this->teamApplication;
    }
}
