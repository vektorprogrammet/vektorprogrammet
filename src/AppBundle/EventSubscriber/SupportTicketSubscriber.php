<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\SupportTicketCreatedEvent;
use AppBundle\Service\EmailSender;
use AppBundle\Service\SlackMessenger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SupportTicketSubscriber implements EventSubscriberInterface
{
    private $emailSender;
    private $session;
    private $logger;
    private $slackMessenger;

    public function __construct(EmailSender $emailSender, SlackMessenger $slackMessenger, SessionInterface $session, LoggerInterface $logger)
    {
        $this->emailSender    = $emailSender;
        $this->session        = $session;
        $this->logger         = $logger;
        $this->slackMessenger = $slackMessenger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SupportTicketCreatedEvent::NAME => array(
                array( 'logEvent', 1 ),
                array( 'sendTicketToDepartment', 0 ),
                array( 'sendTicketReceipt', 0 ),
                array( 'sendTicketToDepartmentSlackChannel', 0 ),
                array( 'addFlashMessage', - 1 ),
                array( 'sendSlackNotification', - 2 ),
            ),
        );
    }

    public function sendTicketToDepartment(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $this->emailSender->sendSupportTicketToDepartment($supportTicket);
    }

    public function sendTicketReceipt(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $this->emailSender->sendSupportTicketReceipt($supportTicket);
    }

    public function sendTicketToDepartmentSlackChannel(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();
        if (!$supportTicket->getDepartment()->getSlackChannel()) {
            return;
        }

        $message =
            "Ny henvendelse fra {$supportTicket->getName()} ({$supportTicket->getEmail()}).\n" .
            "Emne: `{$supportTicket->getSubject()}`\n" .
            "```\n" .
            $supportTicket->getBody() .
            '```';

        $this->slackMessenger->messageDepartment($message, $supportTicket->getDepartment());
    }

    public function addFlashMessage(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();
        $message = 'Kontaktforespørsel sendt til '.$supportTicket->getDepartment()->getEmail().', takk for henvendelsen!';

        $this->session->getFlashBag()->add('success', $message);
    }

    public function logEvent(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $this->logger->info(
            "New support ticket from {$supportTicket->getName()}.\n" .
            "Subject: `{$supportTicket->getSubject()}`\n" .
            "```\n" .
            $supportTicket->getBody() .
            '```'
        );
    }

    public function sendSlackNotification(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $notification =
            "{$supportTicket->getDepartment()}: Ny melding mottatt fra *{$supportTicket->getName()}*. Meldingen ble sendt fra et kontaktskjema på vektorprogrammet.no. \n" .
            "Emne: `{$supportTicket->getSubject()}`\n" .
            "Meldingen har blitt videresendt til {$supportTicket->getDepartment()->getEmail()}";

        $this->slackMessenger->notify($notification);
    }
}
