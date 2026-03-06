<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminTeamMembershipWriteResource;
use App\Entity\TeamMembership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamMembershipEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminTeamMembershipWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $id ? $this->em->getRepository(TeamMembership::class)->find($id) : null;

        if ($membership === null) {
            throw new NotFoundHttpException('Team membership not found.');
        }

        $resource = new AdminTeamMembershipWriteResource();
        $resource->id = $membership->getId();
        $resource->positionId = $membership->getPosition()?->getId();
        $resource->startSemesterId = $membership->getStartSemester()?->getId();
        $resource->endSemesterId = $membership->getEndSemester()?->getId();

        return $resource;
    }
}
