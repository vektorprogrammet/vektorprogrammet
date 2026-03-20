<?php

namespace App\Interview\Domain\Events;

use App\Admission\Infrastructure\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewConductedEvent extends Event
{
    public const NAME = 'interview.conducted';

    /**
     * InterviewConductedEvent constructor.
     */
    public function __construct(private readonly Application $application)
    {
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}
