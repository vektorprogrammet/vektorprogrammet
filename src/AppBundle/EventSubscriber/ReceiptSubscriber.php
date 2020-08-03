<?php


namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Receipt;
use AppBundle\Event\ReceiptEvent;
use AppBundle\Service\EmailSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReceiptSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $emailSender;
    private $session;
    private $tokenStorage;

    public function __construct(LoggerInterface $logger, EmailSender $emailSender, SessionInterface $session, TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger;
        $this->emailSender = $emailSender;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ReceiptEvent::CREATED => array(
                array('sendCreatedEmail', 1),
                array('addCreatedFlashMessage', 1)
            ),
            ReceiptEvent::PENDING => array(
                array('logPendingEvent', 1),
                array('addPendingFlashMessage', 1)
            ),
            ReceiptEvent::REFUNDED => array(
                array('logRefundedEvent', 1),
                array('sendRefundedEmail', 1),
                array('addRefundedFlashMessage', 1)
            ),
            ReceiptEvent::REJECTED => array(
                array('logRejectedEvent', 1),
                array('sendRejectedEmail', 1),
                array('addRejectedFlashMessage', 1)
            ),
            ReceiptEvent::EDITED => array(
                array('addEditedFlashMessage', 1)
            ),
            ReceiptEvent::DELETED => array(
                array('addDeletedFlashMessage', 1)
            )
        );
    }

    public function addCreatedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Utlegget ditt har blitt registrert.');
    }

    public function logPendingEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        $status = $receipt->getStatus();

        $this->logger->info($user->getDepartment() . ": $loggedInUser has changed status of receipt *$visualID* belonging to *$user* to $status");
    }

    public function logRefundedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment() . ": Receipt *$visualID* belonging to *$user* has been refunded by $loggedInUser.");
    }

    public function logRejectedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment() . ": Receipt *$visualID* belonging to *$user* has been rejected by $loggedInUser.");
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

        $this->session->getFlashBag()->add('success', $message);
    }

    public function addRefundedFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som refundert og bekreftelsesmail sendt til $email.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function addRejectedFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som avvist og epostvarsel sendt til $email.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function addEditedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Endringene har blitt lagret.');
    }

    public function addDeletedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Utlegget ble slettet.');
    }
}
