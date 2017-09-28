<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\UserEvent;
use AppBundle\Google\GoogleAPI;
use AppBundle\Service\CompanyEmailMaker;
use AppBundle\Service\RoleManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $authorizationChecker;
    private $roleManager;
    private $googleAPI;
    private $emailMaker;

    public function __construct(LoggerInterface $logger, AuthorizationChecker $authorizationChecker, RoleManager $roleManager, GoogleAPI $googleAPI, CompanyEmailMaker $emailMaker)
    {
        $this->logger = $logger;
        $this->authorizationChecker = $authorizationChecker;
        $this->roleManager = $roleManager;
        $this->googleAPI = $googleAPI;
        $this->emailMaker = $emailMaker;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvent::EDITED => array(
                array('logEdited', -2),
                array('updateGSuiteUser', -2),
            ),
            UserEvent::COMPANY_EMAIL_EDITED  => array(
                array('logCompanyEmailEdited', 1),
                array('updateGSuiteUser', -2),
            ),
        );
    }

    public function updateGSuiteUser(UserEvent $event)
    {
        $user = $event->getUser();
        $oldEmail = $event->getOldEmail();
        if ($oldEmail) {
            dump($oldEmail);
            $this->googleAPI->updateUser($oldEmail, $user);
            $this->logger->info("G Suite account for {$user} with email {$user->getCompanyEmail()} has been updated.");
        }
    }

    public function logEdited(UserEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info("$user edited.");
    }

    public function logCompanyEmailEdited(UserEvent $event)
    {
        $user = $event->getUser();
        $email = $user->getCompanyEmail();
        $this->logger->info("$user edited company email, *$email*");
    }
}
