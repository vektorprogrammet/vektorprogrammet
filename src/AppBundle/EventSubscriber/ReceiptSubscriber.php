<?php


namespace AppBundle\EventSubscriber;

use AppBundle\Event\ReceiptCreatedEvent;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReceiptSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $em;

    public function __construct(LoggerInterface $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ReceiptCreatedEvent::NAME => array(
                array('logEvent', 1),
                array('generateAndSetVisualId', 1)
            )
        );
    }

    public function generateAndSetVisualId(ReceiptCreatedEvent $event)
    {
        $receipt = $event->getReceipt();
        $receipt->generateAndSetVisualId();

        $this->em->persist($receipt);
        $this->em->flush();
    }

    public function logEvent(ReceiptCreatedEvent $event)
    {
        $receipt = $event->getReceipt();
        $user = $receipt->getUser();

        $this->logger->info($user->getDepartment() . ": *$user* created a new receipt.");
    }
}
