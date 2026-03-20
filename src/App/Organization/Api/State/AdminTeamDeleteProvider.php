<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminTeamDeleteResource;
use App\Organization\Infrastructure\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminTeamDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $team = $id ? $this->em->getRepository(Team::class)->find($id) : null;

        if ($team === null) {
            throw new NotFoundHttpException('Team not found.');
        }

        $resource = new AdminTeamDeleteResource();
        $resource->id = $team->getId();

        return $resource;
    }
}
