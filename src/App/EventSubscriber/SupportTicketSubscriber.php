<?php

namespace App\EventSubscriber;

use App\Event\SupportTicketCreatedEvent;
use App\Service\EmailSender;
use App\Service\SlackMessenger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SupportTicketSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EmailSender $emailSender, private readonly SlackMessenger $slackMessenger, private readonly RequestStack $requestStack, private readonly LoggerInterface $logger)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            SupportTicketCreatedEvent::NAME => [
                ['logEvent', 1],
                ['sendTicketToDepartment', 0],
                ['sendTicketReceipt', 0],
                ['sendTicketToDepartmentSlackChannel', 0],
                ['addFlashMessage', -1],
                ['sendSlackNotification', -2],
            ],
        ];
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
            "Ny henvendelse fra {$supportTicket->getName()} ({$supportTicket->getEmail()}).\n".
            "Emne: `{$supportTicket->getSubject()}`\n".
            "```\n".
            $supportTicket->getBody().
            '```';

        $this->slackMessenger->messageDepartment($message, $supportTicket->getDepartment());
    }

    public function addFlashMessage(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();
        $message = 'Kontaktforespørsel sendt til '.$supportTicket->getDepartment()->getEmail().', takk for henvendelsen!';

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }

    public function logEvent(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $this->logger->info(
            "New support ticket from {$supportTicket->getName()}.\n".
            "Subject: `{$supportTicket->getSubject()}`\n".
            "```\n".
            $supportTicket->getBody().
            '```'
        );
    }

    public function sendSlackNotification(SupportTicketCreatedEvent $event)
    {
        $supportTicket = $event->getSupportTicket();

        $notification =
            "{$supportTicket->getDepartment()}: Ny melding mottatt fra *{$supportTicket->getName()}*. Meldingen ble sendt fra et kontaktskjema på vektorprogrammet.no. \n".
            "Emne: `{$supportTicket->getSubject()}`\n".
            "Meldingen har blitt videresendt til {$supportTicket->getDepartment()->getEmail()}";

        $this->slackMessenger->notify($notification);
    }
}
