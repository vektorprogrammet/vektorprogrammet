<?php

namespace App\Scheduling\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Scheduling\Api\Resource\AdminSchoolWriteResource;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSchoolEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSchoolWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $school = $id !== null ? $this->em->getRepository(School::class)->find($id) : null;

        if ($school === null) {
            throw new NotFoundHttpException('School not found.');
        }

        $resource = new AdminSchoolWriteResource();
        $resource->id = $school->getId();
        $resource->name = $school->getName();
        $resource->contactPerson = $school->getContactPerson();
        $resource->email = $school->getEmail();
        $resource->phone = $school->getPhone();
        $resource->international = $school->isInternational();
        $resource->active = $school->isActive();

        return $resource;
    }
}
