<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminTeamWriteResource;
use App\Entity\Team;
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
        $team = $id ? $this->em->getRepository(Team::class)->find($id) : null;

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
