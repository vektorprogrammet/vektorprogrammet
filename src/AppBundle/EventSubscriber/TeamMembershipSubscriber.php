<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\TeamMembershipEvent;
use AppBundle\Service\RoleManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class TeamMembershipSubscriber implements EventSubscriberInterface
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
            TeamMembershipEvent::CREATED => array(
                array('updateUserRole', 5),
                array('addCreatedFlashMessage', -1),
            ),
            TeamMembershipEvent::EDITED  => array(
                array('updateUserRole', 5),
                array('addUpdatedFlashMessage', -1),
            ),
            TeamMembershipEvent::DELETED => array(
                array('logDeletedEvent', 10),
                array('updateUserRole', 5),
            ),
        );
    }

    public function addCreatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->session->getFlashBag()->add('success', "$user har blitt lagt til i $team som $position.");
    }

    public function addUpdatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->session->getFlashBag()->add('success', "$user i $team med stilling $position har blitt oppdatert.");
    }

    public function logDeletedEvent(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();
        $team = $teamMembership->getTeam();
        $department = $team->getDepartment();

        $startSemester = $teamMembership->getStartSemester()->getName();
        $endSemester = $teamMembership->getEndSemester();

        $endStr = $endSemester !== null ? 'to '.$endSemester->getName() : '';

        $this->logger->info("TeamMembership deleted: $user (position: $position), active from $startSemester $endStr, was deleted from $team ($department)");
    }

    public function updateUserRole(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();

        $this->roleManager->updateUserRole($user);
    }
}
