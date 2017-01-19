<?php

namespace AppBundle\Event;

use AppBundle\Entity\SupportTicket;
use Symfony\Component\EventDispatcher\Event;

class SupportTicketCreatedEvent extends Event
{
    const NAME = 'support_ticket.created';

    private $supportTicket;

    public function __construct(SupportTicket $supportTicket)
    {
        $this->supportTicket = $supportTicket;
    }

    /**
     * @return SupportTicket
     */
    public function getSupportTicket(): SupportTicket
    {
        return $this->supportTicket;
    }
}
