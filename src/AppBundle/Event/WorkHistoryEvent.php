<?php

namespace AppBundle\Event;

use AppBundle\Entity\WorkHistory;
use Symfony\Component\EventDispatcher\Event;

class WorkHistoryEvent extends Event
{
    const CREATED = 'work_history.created';
    const EDITED = 'work_history.edited';
    const DELETED = 'work_history.deleted';

    private $workHistory;

    /**
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
