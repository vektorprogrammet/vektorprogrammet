<?php

namespace AppBundle\Event;

use AppBundle\Entity\Interview;
use Symfony\Component\EventDispatcher\Event;

class InterviewEvent extends Event
{
    const SCHEDULE = 'interview.schedule';
    const COASSIGN = 'interview.coassign';

    private $interview;
    private $data;

    /**
     * ReceiptEvent constructor.
     *
     * @param Interview $interview
     * @param $data
     */
    public function __construct(Interview $interview, $data = [])
    {
        $this->interview = $interview;
        $this->data = $data;
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
