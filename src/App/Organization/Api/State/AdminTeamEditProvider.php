<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminTeamWriteResource;
use App\Organization\Infrastructure\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminTeamWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $team = $id !== null ? $this->em->getRepository(Team::class)->find($id) : null;

        if ($team === null) {
            throw new NotFoundHttpException('Team not found.');
        }

        $resource = new AdminTeamWriteResource();
        $resource->id = $team->getId();
        $resource->name = $team->getName() ?? '';
        $resource->email = $team->getEmail();
        $resource->shortDescription = $team->getShortDescription();
        $resource->description = $team->getDescription();

        return $resource;
    }
}
