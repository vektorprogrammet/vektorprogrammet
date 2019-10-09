<?php

namespace AppBundle\Service;

use AppBundle\Event\TeamMembershipEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipService
{
    private $em;
    private $dispatcher;

    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function updateTeamMemberships()
    {
        $teamMemberships = $this->em->getRepository('AppBundle:TeamMembership')->findActiveTeamMemberships();
        foreach ($teamMemberships as $teamMembership) {
            $endSemester = $teamMembership->getEndSemester();
            if ($endSemester) {
                if ($endSemester->getSemesterEndDate() <= $this->em->getRepository('AppBundle:Semester')->findCurrentSemester()->getSemesterStartDate()) {
                    $this->dispatcher->dispatch(TeamMembershipEvent::DELETED, new TeamMembershipEvent($teamMembership));
                }
            }
        }
        return $teamMemberships;
    }
}
