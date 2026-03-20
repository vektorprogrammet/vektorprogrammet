<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminTeamMembershipDeleteResource;
use App\Organization\Infrastructure\Entity\TeamMembership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamMembershipDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminTeamMembershipDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $id ? $this->em->getRepository(TeamMembership::class)->find($id) : null;

        if ($membership === null) {
            throw new NotFoundHttpException('Team membership not found.');
        }

        $resource = new AdminTeamMembershipDeleteResource();
        $resource->id = $membership->getId();

        return $resource;
    }
}
