<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\WorkHistoryEvent;
use AppBundle\Service\RoleManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class WorkHistorySubscriber implements EventSubscriberInterface
{
    private $session;
    private $logger;
    private $roleManager;

    public function __construct(Session $session, LoggerInterface $logger, RoleManager $roleManager)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->roleManager = $roleManager;
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
                array('logCreatedEvent', 10),
                array('updateUserRole', 5),
                array('addCreatedFlashMessage', -1),
            ),
            WorkHistoryEvent::EDITED  => array(
                array('logEditedEvent', 10),
                array('updateUserRole', 5),
                array('addUpdatedFlashMessage', -1),
            ),
            WorkHistoryEvent::DELETED => array(
                array('logDeletedEvent', 10),
                array('updateUserRole', 5),
            ),
        );
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
