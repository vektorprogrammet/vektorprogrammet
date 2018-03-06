<?php

namespace AppBundle\Event;

use AppBundle\Entity\AssistantHistory;
use Symfony\Component\EventDispatcher\Event;

class AssistantHistoryEditedEvent extends Event implements CrudEvent
{
    const CREATED = 'AssistantHistory.created';
    const REFUNDED = 'AssistantHistory.refunded';
    const REJECTED = 'AssistantHistory.rejected';
    const PENDING = 'AssistantHistory.pending';
    const EDITED = 'AssistantHistory.edited';
    const DELETED = 'AssistantHistory.deleted';

    private $AssistantHistory;

    /**
     * AssistantHistoryEvent constructor.
     *
     * @param AssistantHistory $AssistantHistory
     */
    public function __construct(AssistantHistory $AssistantHistory)
    {
        $this->AssistantHistory = $AssistantHistory;
    }

    /**
     * @return AssistantHistory
     */
    public function getAssistantHistory(): AssistantHistory
    {
        return $this->AssistantHistory;
    }

    public function getObject()
    {
        return $this->getAssistantHistory();
    }

    public static function created(): string
    {
        return self::CREATED;
    }

    public static function updated(): string
    {
        return self::EDITED;
    }

    public static function deleted(): string
    {
        return self::DELETED;
    }
}
