<?php

namespace App\EventSubscriber;

use App\Event\TeamMembershipEvent;
use App\Service\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TeamMembershipSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RequestStack $requestStack, private readonly LoggerInterface $logger, private readonly RoleManager $roleManager, private readonly EntityManagerInterface $em)
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
            TeamMembershipEvent::CREATED => [
                ['updateUserRole', 5],
                ['activateTeamMembership', 2],
                ['addCreatedFlashMessage', -1],
            ],
            TeamMembershipEvent::EDITED => [
                ['updateUserRole', 5],
                ['activateTeamMembership', 2],
                ['addUpdatedFlashMessage', -1],
            ],
            TeamMembershipEvent::DELETED => [
                ['logDeletedEvent', 10],
                ['updateUserRole', 5],
            ],
        ];
    }

    public function addCreatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->requestStack->getSession()->getFlashBag()->add('success', "$user har blitt lagt til i $team som $position.");
    }

    public function addUpdatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->requestStack->getSession()->getFlashBag()->add('success', "$user i $team med stilling $position har blitt oppdatert.");
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

    public function activateTeamMembership(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();
        $now = new \DateTime();
        if ($teamMembership->getEndSemester() === null || $teamMembership->getEndSemester()->getEndDate() > $now) {
            $teamMembership->setIsSuspended(false);
        }
        $this->em->persist($teamMembership);
        $this->em->flush();
    }

    public function updateUserRole(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();

        $this->roleManager->updateUserRole($user);
    }
}
