<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\UserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvent::COMPANY_EMAIL_EDITED  => array(
                array('logCompanyEmailEdited', 1),
            ),
        );
    }

    public function logCompanyEmailEdited(UserEvent $event)
    {
        $user = $event->getUser();
        $email = $user->getCompanyEmail();
        $this->logger->info("$user edited company email, *$email*");
    }
}
