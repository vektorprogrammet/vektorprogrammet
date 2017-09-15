<?php


namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Receipt;
use AppBundle\Event\ReceiptEvent;
use AppBundle\Service\EmailSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ReceiptSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $emailSender;
    private $session;
    private $tokenStorage;

    public function __construct(LoggerInterface $logger, EmailSender $emailSender, Session $session, TokenStorage $tokenStorage)
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
                array('logCreatedEvent', 1),
                array('sendCreatedEmail', 1),
                array('addCreatedFlashMessage', 1)
            ),
            ReceiptEvent::REFUNDED => array(
                array('logRefundedEvent', 1),
                array('sendRefundedEmail', 1),
                array('addRefundedFlashMessage', 1)
            ),
            ReceiptEvent::CANCELLED => array(
                array('logCancelledEvent', 1),
                array('sendCancelEmail', 1),
                array('addCancelFlashMessage', 1)
            ),
            ReceiptEvent::EDITED => array(
                array('logEditedEvent', 1),
                array('addEditedFlashMessage', 1)
            ),
            ReceiptEvent::DELETED => array(
                array('logDeletedEvent', 1),
                array('addDeletedFlashMessage', 1)
            )
        );
    }

    public function logCreatedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualId = $receipt->getVisualId();

        $this->logger->info($user->getDepartment() . ": *$user* created a new receipt with id *$visualId*.");
    }

    public function addCreatedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Utlegget ditt har blitt registrert.');
    }

    public function logRefundedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment() . ": Receipt *$visualID* belonging to *$user* has been refunded by $loggedInUser.");
    }

    public function logCancelledEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($user->getDepartment() . ": Receipt *$visualID* belonging to *$user* has been cancelled by $loggedInUser.");
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

    public function sendCancelEmail(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();

        $this->emailSender->sendCancelReceiptConfirmation($receipt);
    }

    public function addRefundedFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som refundert og bekreftelsesmail sendt til $email.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function addCancelFlashMessage(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $email = $receipt->getUser()->getEmail();
        $message = "Utlegget ble markert som kansellert og bekreftelsesmail sendt til $email.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function logEditedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $owner = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($owner->getDepartment() . ": Receipt *$visualID* belonging to $owner was edited by $loggedInUser.");
    }

    public function addEditedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Endringene har blitt lagret.');
    }

    public function logDeletedEvent(ReceiptEvent $event)
    {
        $receipt = $event->getReceipt();
        $owner = $receipt->getUser();
        $visualID = $receipt->getVisualId();
        $loggedInUser = $this->tokenStorage->getToken()->getUser();

        $this->logger->info($owner->getDepartment() . ": Receipt *$visualID* belonging to $owner has been deleted by $loggedInUser.");
    }

    public function addDeletedFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Utlegget ble slettet.');
    }
}
