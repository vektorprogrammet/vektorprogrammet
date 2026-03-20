<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminDepartmentWriteResource;
use App\Organization\Infrastructure\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminDepartmentEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminDepartmentWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $department = $id ? $this->em->getRepository(Department::class)->find($id) : null;

        if ($department === null) {
            throw new NotFoundHttpException('Department not found.');
        }

        $resource = new AdminDepartmentWriteResource();
        $resource->id = $department->getId();
        $resource->name = $department->getName();
        $resource->shortName = $department->getShortName();
        $resource->email = $department->getEmail();
        $resource->city = $department->getCity();
        $resource->address = $department->getAddress();
        $resource->latitude = $department->getLatitude();
        $resource->longitude = $department->getLongitude();

        return $resource;
    }
}
