<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSchoolDeleteResource;
use App\Entity\School;
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
        $school = $id ? $this->em->getRepository(School::class)->find($id) : null;

        if ($school === null) {
            throw new NotFoundHttpException('School not found.');
        }

        $resource = new AdminSchoolDeleteResource();
        $resource->id = $school->getId();

        return $resource;
    }
}
