<?php

namespace App\Operations\Infrastructure\Subscriber;

use App\Operations\Domain\Events\ReceiptEvent;
use App\Service\EmailSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReceiptSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly EmailSender $emailSender, private readonly RequestStack $requestStack, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ReceiptEvent::CREATED => [
                ['sendCreatedEmail', 1],
                ['addCreatedFlashMessage', 1],
            ],
            ReceiptEvent::PENDING => [
                ['logPendingEvent', 1],
                ['addPendingFlashMessage', 1],
            ],
            ReceiptEvent::REFUNDED => [
                ['logRefundedEvent', 1],
                ['sendRefundedEmail', 1],
                ['addRefundedFlashMessage', 1],
            ],
            ReceiptEvent::REJECTED => [
                ['logRejectedEvent', 1],
                ['sendRejectedEmail', 1],
                ['addRejectedFlashMessage', 1],
            ],
            ReceiptEvent::EDITED => [
                ['addEditedFlashMessage', 1],
            ],
            ReceiptEvent::DELETED => [
                ['addDeletedFlashMessage', 1],
            ],
        ];
    }

    public function addCreatedFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Utlegget ditt har blitt registrert.');
    }

    public function logPendingEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        $status = $receipt->getStatus();

        $this->logger->info($user->getDepartment().": $loggedInUser has changed status of receipt *$visualID* belonging to *$user* to $status");
    }

    public function logRefundedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment().": Receipt *$visualID* belonging to *$user* has been refunded by $loggedInUser.");
    }

    public function logRejectedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment().": Receipt *$visualID* belonging to *$user* has been rejected by $loggedInUser.");
    }

    public function sendCreatedEmail(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendReceiptCreatedNotification($receipt);
    }

    public function sendRefundedEmail(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendPaidReceiptConfirmation($receipt);
    }

    public function sendRejectedEmail(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendRejectedReceiptConfirmation($receipt);
    }

    public function addPendingFlashMessage()
    {
        $message = "Utlegget ble markert som 'Venter behandling'.";

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }

    public function addRefundedFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som refundert og bekreftelsesmail sendt til $email.";

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }

    public function addRejectedFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som avvist og epostvarsel sendt til $email.";

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }

    public function addEditedFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Endringene har blitt lagret.');
    }

    public function addDeletedFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Utlegget ble slettet.');
    }
}
