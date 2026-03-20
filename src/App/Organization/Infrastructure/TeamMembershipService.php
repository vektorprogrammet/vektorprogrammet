<?php

namespace App\Organization\Infrastructure;

use App\Organization\Domain\Events\TeamMembershipEvent;
use App\Organization\Domain\Rules\MembershipExpiration;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly MembershipExpiration $expirationRule,
    ) {
    }

    public function updateTeamMemberships()
    {
        $teamMemberships = $this->em->getRepository(TeamMembership::class)->findBy(['isSuspended' => false]);
        $currentSemesterStartDate = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester()->getStartDate();
        foreach ($teamMemberships as $teamMembership) {
            if ($this->expirationRule->isExpired($teamMembership, $currentSemesterStartDate)) {
                $teamMembership->setIsSuspended(true);
                $this->dispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::EXPIRED);
            }
        }
        $this->em->flush();

        return $teamMemberships;
    }
}
