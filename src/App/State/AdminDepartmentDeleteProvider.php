<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminDepartmentDeleteResource;
use App\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminDepartmentDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminDepartmentDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $department = $id ? $this->em->getRepository(Department::class)->find($id) : null;

        if ($department === null) {
            throw new NotFoundHttpException('Department not found.');
        }

        $resource = new AdminDepartmentDeleteResource();
        $resource->id = $department->getId();

        return $resource;
    }
}
