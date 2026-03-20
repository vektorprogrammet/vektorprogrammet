<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminAdmissionPeriodCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdmissionPeriodRepository $admissionPeriodRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $department = $this->em->getRepository(Department::class)->find($data->departmentId);
        if ($department === null) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        $semester = $this->em->getRepository(Semester::class)->find($data->semesterId);
        if ($semester === null) {
            throw new UnprocessableEntityHttpException('Semester not found.');
        }

        // Check for duplicate department+semester combination
        $existing = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);
        if ($existing !== null) {
            throw new ConflictHttpException('Admission period already exists for this department and semester.');
        }

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate(new \DateTime($data->startDate));
        $admissionPeriod->setEndDate(new \DateTime($data->endDate));

        $this->em->persist($admissionPeriod);
        $this->em->flush();

        return ['id' => $admissionPeriod->getId()];
    }
}
