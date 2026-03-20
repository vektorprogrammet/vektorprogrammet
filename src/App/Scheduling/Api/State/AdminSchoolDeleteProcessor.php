<?php

namespace App\Scheduling\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

class AdminSchoolDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $school = $this->em->getRepository(School::class)->find($id);

        if ($school === null) {
            return;
        }

        $this->em->remove($school);
        $this->em->flush();
    }
}
