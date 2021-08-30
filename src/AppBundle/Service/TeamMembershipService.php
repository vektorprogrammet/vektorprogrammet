<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Entity\TeamMembership;
use AppBundle\Event\TeamMembershipEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipService
{
    private $em;
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function updateTeamMemberships()
    {
        $teamMemberships = $this->em->getRepository(TeamMembership::class)->findBy(array('isSuspended' => false));
        $currentSemesterStartDate = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester()->getStartDate();
        foreach ($teamMemberships as $teamMembership) {
            $endSemester = $teamMembership->getEndSemester();
            if ($endSemester) {
                if ($endSemester->getEndDate() <= $currentSemesterStartDate) {
                    $teamMembership->setIsSuspended(true);
                    $this->dispatcher->dispatch(TeamMembershipEvent::EXPIRED, new TeamMembershipEvent($teamMembership));
                }
            }
        }
        $this->em->flush();
        return $teamMemberships;
    }
}
