<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;

class AdminDepartmentCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $department = new Department();
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
