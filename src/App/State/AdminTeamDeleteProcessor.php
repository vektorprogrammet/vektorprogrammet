<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Team;
use App\Entity\TeamMembership;
use Doctrine\ORM\EntityManagerInterface;

class AdminTeamDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $team = $this->em->getRepository(Team::class)->find($id);

        if ($team === null) {
            return;
        }

        // Query memberships explicitly -- lazy-loaded collection may miss recently-created ones
        $memberships = $this->em->getRepository(TeamMembership::class)->findBy(['team' => $team]);
        $teamName = $team->getName();
        foreach ($memberships as $membership) {
            $membership->setDeletedTeamName($teamName);
            $this->em->persist($membership);
        }

        $this->em->remove($team);
        $this->em->flush();
    }
}
