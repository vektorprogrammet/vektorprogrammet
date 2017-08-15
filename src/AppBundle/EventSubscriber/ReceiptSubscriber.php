<?php


namespace AppBundle\EventSubscriber;

use AppBundle\Event\ReceiptCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReceiptSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ReceiptCreatedEvent::NAME => array(
                array('logEvent', 1)
            )
        );
    }

    public function logEvent(ReceiptCreatedEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();

        $this->logger->info($user->getDepartment() . ": *$user* created a new receipt.");
    }
}
