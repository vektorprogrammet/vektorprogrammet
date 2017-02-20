<?php

namespace AppBundle\Event;

use AppBundle\Entity\TeamApplication;
use Symfony\Component\EventDispatcher\Event;

class TeamApplicationCreatedEvent extends Event
{
    const NAME = 'team_application.created';
    /**
     * @var TeamApplication
     */
    private $teamApplication;

    /**
     * TeamApplicationCreatedEvent constructor.
     *
     * @param TeamApplication $teamApplication
     */
    public function __construct(TeamApplication $teamApplication)
    {
        $this->teamApplication = $teamApplication;
    }

    /**
     * @return TeamApplication
     */
    public function getTeamApplication()
    {
        return $this->teamApplication;
    }
}
