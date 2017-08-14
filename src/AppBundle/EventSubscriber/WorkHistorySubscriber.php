<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\WorkHistoryEvent;
use AppBundle\Event\WorkHistoryEditedEvent;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class WorkHistorySubscriber implements EventSubscriberInterface
{
    private $session;
    private $logger;
    private $authorizationChecker;
    private $em;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param Session              $session
     * @param LoggerInterface      $logger
     * @param AuthorizationChecker $authorizationChecker
     * @param EntityManager        $em
     */
    public function __construct(Session $session, LoggerInterface $logger, AuthorizationChecker $authorizationChecker, EntityManager $em)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->authorizationChecker = $authorizationChecker;
        $this->em = $em;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            WorkHistoryEvent::CREATED        => array(
                array('logCreatedEvent', 1),
                array('updateUserRole', 0),
                array('addCreatedFlashMessage', -1),
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
        $workHistory = $event->getWorkHistory();

        $user = $event->getWorkHistory()->getUser();
        $department = $user->getDepartment();
        $currentSemester = $department->getCurrentSemester();

        $isInTeamCurrentSemester = $workHistory->isActiveInSemester($currentSemester);

        $userNeedsToBePromoted = empty($user->getRoles()) || current($user->getRoles())->getRole() === Roles::ASSISTANT;

        if ($isInTeamCurrentSemester && $userNeedsToBePromoted) {
            $role = $this->em->getRepository('AppBundle:Role')->findByRoleName(Roles::TEAM_MEMBER);
            $user->setRoles([$role]);

            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
