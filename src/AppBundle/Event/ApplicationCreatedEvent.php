<?php

namespace AppBundle\Event;

use AppBundle\Entity\Application;
use Symfony\Component\EventDispatcher\Event;

class ApplicationCreatedEvent extends Event
{
    const NAME = 'application.admission';

    private $application;

    /**
     * ApplicationAdmissionEvent constructor.
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
