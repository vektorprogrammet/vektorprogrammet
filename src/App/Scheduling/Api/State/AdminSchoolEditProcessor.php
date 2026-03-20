<?php

namespace App\Scheduling\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSchoolEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $school = $this->em->getRepository(School::class)->find($id);

        if ($school === null) {
            throw new NotFoundHttpException('School not found.');
        }

        $school->setName($data->name);
        $school->setContactPerson($data->contactPerson);
        $school->setEmail($data->email);
        $school->setPhone($data->phone);
        $school->setInternational($data->international);
        $school->setActive($data->active);

        $this->em->persist($school);
        $this->em->flush();

        return [
            'id' => $school->getId(),
            'name' => $school->getName(),
            'contactPerson' => $school->getContactPerson(),
            'email' => $school->getEmail(),
            'phone' => $school->getPhone(),
            'international' => $school->isInternational(),
            'active' => $school->isActive(),
        ];
    }
}
