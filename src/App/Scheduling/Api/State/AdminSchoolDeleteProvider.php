<?php

namespace App\Scheduling\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Scheduling\Api\Resource\AdminSchoolDeleteResource;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSchoolDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSchoolDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $school = $id !== null ? $this->em->getRepository(School::class)->find($id) : null;

        if ($school === null) {
            throw new NotFoundHttpException('School not found.');
        }

        $resource = new AdminSchoolDeleteResource();
        $resource->id = $school->getId();

        return $resource;
    }
}
