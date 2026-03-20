<?php

namespace App\Operations\Infrastructure\Subscriber;

use App\Operations\Domain\Events\ReceiptEvent;
use App\Admission\Infrastructure\EmailSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
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

    private function addFlash(string $type, string $message): void
    {
        $session = $this->requestStack->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add($type, $message);
        }
    }

    public function addCreatedFlashMessage(): void
    {
        $this->addFlash('success', 'Utlegget ditt har blitt registrert.');
    }

    public function logPendingEvent(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        $status = $receipt->getStatus();

        $this->logger->info($user->getDepartment().": $loggedInUser has changed status of receipt *$visualID* belonging to *$user* to $status");
    }

    public function logRefundedEvent(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment().": Receipt *$visualID* belonging to *$user* has been refunded by $loggedInUser.");
    }

    public function logRejectedEvent(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment().": Receipt *$visualID* belonging to *$user* has been rejected by $loggedInUser.");
    }

    public function sendCreatedEmail(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendReceiptCreatedNotification($receipt);
    }

    public function sendRefundedEmail(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendPaidReceiptConfirmation($receipt);
    }

    public function sendRejectedEmail(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendRejectedReceiptConfirmation($receipt);
    }

    public function addPendingFlashMessage(): void
    {
        $this->addFlash('success', "Utlegget ble markert som 'Venter behandling'.");
    }

    public function addRefundedFlashMessage(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $this->addFlash('success', "Utlegget ble markert som refundert og bekreftelsesmail sendt til $email.");
    }

    public function addRejectedFlashMessage(ReceiptEvent $event): void
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $this->addFlash('success', "Utlegget ble markert som avvist og epostvarsel sendt til $email.");
    }

    public function addEditedFlashMessage(): void
    {
        $this->addFlash('success', 'Endringene har blitt lagret.');
    }

    public function addDeletedFlashMessage(): void
    {
        $this->addFlash('success', 'Utlegget ble slettet.');
    }
}
