<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminDepartmentEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $department = $this->em->getRepository(Department::class)->find($id);

        if ($department === null) {
            throw new NotFoundHttpException('Department not found.');
        }

        $department->setName($data->name);
        $department->setShortName($data->shortName);
        $department->setEmail($data->email);
        $department->setCity($data->city);

        if ($data->address !== null) {
            $department->setAddress($data->address);
        }
        if ($data->latitude !== null) {
            $department->setLatitude($data->latitude);
        }
        if ($data->longitude !== null) {
            $department->setLongitude($data->longitude);
        }

        $this->em->persist($department);
        $this->em->flush();

        return ['id' => $department->getId()];
    }
}
