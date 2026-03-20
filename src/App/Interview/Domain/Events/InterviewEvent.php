<?php

namespace App\Interview\Domain\Events;

use App\Interview\Infrastructure\Entity\Interview;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewEvent extends Event
{
    public const SCHEDULE = 'interview.schedule';
    public const COASSIGN = 'interview.coassign';

    /**
     * ReceiptEvent constructor.
     */
    public function __construct(private readonly Interview $interview, private $data = [])
    {
    }

    /**
     * @return Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
