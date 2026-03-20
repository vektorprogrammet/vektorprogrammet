<?php

namespace App\Shared\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;

class AdminSemesterDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $semester = $this->em->getRepository(Semester::class)->find($id);

        if ($semester !== null) {
            $this->em->remove($semester);
            $this->em->flush();
        }
    }
}
