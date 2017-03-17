<?php

namespace AppBundle\Event;

use AppBundle\Entity\WorkHistory;
use Symfony\Component\EventDispatcher\Event;

class WorkHistoryCreatedEvent extends Event
{
    const NAME = 'work_history.created';

    private $workHistory;

    /**
     * WorkHistoryCreatedEvent constructor.
     *
     * @param WorkHistory $workHistory
     */
    public function __construct(WorkHistory $workHistory)
    {
        $this->workHistory = $workHistory;
    }

    /**
     * @return WorkHistory
     */
    public function getWorkHistory(): WorkHistory
    {
        return $this->workHistory;
    }
}
