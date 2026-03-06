<?php

namespace App\Event;

use App\Entity\AssistantHistory;
use Symfony\Contracts\EventDispatcher\Event;

class AssistantHistoryCreatedEvent extends Event
{
    public const NAME = 'assistant_history.created';

    /**
     * ApplicationAdmissionEvent constructor.
     */
    public function __construct(private readonly AssistantHistory $assistantHistory)
    {
    }

    public function getAssistantHistory(): AssistantHistory
    {
        return $this->assistantHistory;
    }
}
