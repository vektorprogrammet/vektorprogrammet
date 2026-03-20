<?php

namespace App\Admission\Domain\Events;

use App\Admission\Infrastructure\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

class ApplicationCreatedEvent extends Event
{
    public const NAME = 'application.admission';

    /**
     * ApplicationAdmissionEvent constructor.
     */
    public function __construct(private readonly Application $application)
    {
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}
