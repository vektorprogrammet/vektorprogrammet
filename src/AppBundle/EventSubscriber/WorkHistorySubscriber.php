<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\WorkHistoryEvent;
use AppBundle\Google\GoogleAPI;
use AppBundle\Service\CompanyEmailMaker;
use AppBundle\Service\RoleManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class WorkHistorySubscriber implements EventSubscriberInterface
{
    private $session;
    private $logger;
    private $authorizationChecker;
    private $roleManager;
    private $googleAPI;
    private $emailMaker;

    public function __construct(Session $session, LoggerInterface $logger, AuthorizationChecker $authorizationChecker, RoleManager $roleManager, GoogleAPI $googleAPI, CompanyEmailMaker $emailMaker)
    {
        $this->session = $session;
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
            WorkHistoryEvent::CREATED => array(
                array('logCreatedEvent', 1),
                array('updateUserRole', 0),
                array('addCreatedFlashMessage', -1),
                array('createGSuiteUser', -2),
            ),
            WorkHistoryEvent::EDITED  => array(
                array('logEditedEvent', 1),
                array('updateUserRole', 0),
                array('addUpdatedFlashMessage', -1),
            ),
            WorkHistoryEvent::DELETED => array(
                array('logDeletedEvent', 1),
                array('updateUserRole', 0),
            ),
        );
    }

    public function createGSuiteUser(WorkHistoryEvent $event)
    {
        $user = $event->getWorkHistory()->getUser();
        if (!$user->getCompanyEmail()) {
            $emailsInUse = $this->googleAPI->getUsers();
            $emailsInUse = array_map(function ($e) {
                return $e->primaryEmail;
            }, $emailsInUse);

            $email = $this->emailMaker->setCompanyEmailFor($user, $emailsInUse);

            if ($email !== null) {
                $this->googleAPI->createUser($user);
                $this->logger->info("New G Suite account created for {$user} with email {$user->getCompanyEmail()}");
            }
        }
    }

    public function addCreatedFlashMessage(WorkHistoryEvent $event)
    {
        $workHistory = $event->getWorkHistory();

        $team = $workHistory->getTeam();
        $user = $workHistory->getUser();
        $position = $workHistory->getPosition();

        $this->session->getFlashBag()->add('success', "$user har blitt lagt til i $team som $position.");
    }

    public function addUpdatedFlashMessage(WorkHistoryEvent $event)
    {
        $workHistory = $event->getWorkHistory();

        $team = $workHistory->getTeam();
        $user = $workHistory->getUser();
        $position = $workHistory->getPosition();

        $this->session->getFlashBag()->add('success', "$user i $team med stilling $position har blitt oppdatert.");
    }

    public function logCreatedEvent(WorkHistoryEvent $event)
    {
        $workHistory = $event->getWorkHistory();

        $user = $workHistory->getUser();
        $position = $workHistory->getPosition();
        $team = $workHistory->getTeam();
        $department = $team->getDepartment();

        $startSemester = $workHistory->getStartSemester()->getName();
        $endSemester = $workHistory->getEndSemester();

        $endStr = $endSemester !== null ? 'to '.$endSemester->getName() : '';

        $this->logger->info(" $user has joined $team ($department) as $position from $startSemester $endStr");
    }

    public function logEditedEvent(WorkHistoryEvent $event)
    {
        $workHistory = $event->getWorkHistory();

        $user = $workHistory->getUser();
        $position = $workHistory->getPosition();
        $team = $workHistory->getTeam();
        $department = $team->getDepartment();

        $startSemester = $workHistory->getStartSemester()->getName();
        $endSemester = $workHistory->getEndSemester();

        $endStr = $endSemester !== null ? 'to '.$endSemester->getName() : '';

        $this->logger->info("WorkHistory edited: $user has joined $team ($department) as $position from $startSemester $endStr");
    }

    public function logDeletedEvent(WorkHistoryEvent $event)
    {
        $workHistory = $event->getWorkHistory();

        $user = $workHistory->getUser();
        $position = $workHistory->getPosition();
        $team = $workHistory->getTeam();
        $department = $team->getDepartment();

        $startSemester = $workHistory->getStartSemester()->getName();
        $endSemester = $workHistory->getEndSemester();

        $endStr = $endSemester !== null ? 'to '.$endSemester->getName() : '';

        $this->logger->info("WorkHistory deleted: $user (position: $position), active from $startSemester $endStr, was deleted from $team ($department)");
    }

    public function updateUserRole(WorkHistoryEvent $event)
    {
        $user = $event->getWorkHistory()->getUser();

        $this->roleManager->updateUserRole($user);
    }
}
