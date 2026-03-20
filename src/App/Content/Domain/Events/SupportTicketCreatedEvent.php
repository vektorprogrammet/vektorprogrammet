<?php

namespace App\Content\Domain\Events;

use App\Content\Infrastructure\Entity\SupportTicket;
use Symfony\Contracts\EventDispatcher\Event;

class SupportTicketCreatedEvent extends Event
{
    public const NAME = 'support_ticket.created';

    public function __construct(private readonly SupportTicket $supportTicket)
    {
    }

    public function getSupportTicket(): SupportTicket
    {
        return $this->supportTicket;
    }
}
