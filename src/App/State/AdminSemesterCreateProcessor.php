<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AdminSemesterCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly SemesterRepository $semesterRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $existing = $this->semesterRepository->findByTimeAndYear($data->semesterTime, $data->year);

        if ($existing !== null) {
            throw new ConflictHttpException('Semester already exists.');
        }

        $semester = new Semester();
        $semester->setSemesterTime($data->semesterTime);
        $semester->setYear($data->year);

        $this->em->persist($semester);
        $this->em->flush();

        return ['id' => $semester->getId()];
    }
}
