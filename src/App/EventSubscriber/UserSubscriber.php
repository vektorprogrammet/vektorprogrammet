<?php

namespace App\EventSubscriber;

use App\Event\UserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
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
            UserEvent::COMPANY_EMAIL_EDITED => [
                ['logCompanyEmailEdited', 1],
            ],
        ];
    }

    public function logCompanyEmailEdited(UserEvent $event)
    {
        $user = $event->getUser();
        $email = $user->getCompanyEmail();
        $this->logger->info("$user edited company email, *$email*");
    }
}
