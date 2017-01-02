<?php

namespace AppBundle\Event;

use AppBundle\Entity\Application;
use Symfony\Component\EventDispatcher\Event;

class InterviewConductedEvent extends Event
{
    const NAME = 'interview.conducted';

    private $application;

    /**
     * InterviewConductedEvent constructor.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}
