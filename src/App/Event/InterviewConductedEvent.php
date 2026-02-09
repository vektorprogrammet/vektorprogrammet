<?php

namespace App\Event;

use App\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

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
