<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Service\Contract\SemesterManagementServiceInterface;
use AppBundle\Service\Contract\SemesterValidationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing semesters and semester-related business logic.
 */
class SemesterManagementService implements SemesterManagementServiceInterface
{
    private $entityManager;
    private $semesterValidationService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SemesterValidationServiceInterface $semesterValidationService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SemesterValidationServiceInterface $semesterValidationService
    ) {
        $this->entityManager = $entityManager;
        $this->semesterValidationService = $semesterValidationService;
    }

    /**
     * {@inheritdoc}
     */
    public function createSemester(Semester $semester): array
    {
        $existingSemester = $this->semesterValidationService->findExistingSemester(
            $semester->getSemesterTime(),
            $semester->getYear()
        );

        if ($existingSemester !== null) {
            return [
                'success' => false,
                'existingSemester' => $existingSemester,
            ];
        }

        $this->entityManager->persist($semester);
        $this->entityManager->flush();

        return [
            'success' => true,
            'existingSemester' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSemester(Semester $semester): void
    {
        $this->entityManager->remove($semester);
        $this->entityManager->flush();
    }
}

