<?php

namespace App\Service;

use App\Models\Department;
use App\Models\FieldOfStudy;
use App\Repository\Contract\FieldOfStudyRepositoryInterface;
use App\Service\Contract\FieldOfStudyManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing fields of study and field of study-related business logic.
 */
class FieldOfStudyManagementService implements FieldOfStudyManagementServiceInterface
{
    private $fieldOfStudyRepository;
    private $entityManager;

    /**
     * @param FieldOfStudyRepositoryInterface $fieldOfStudyRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FieldOfStudyRepositoryInterface $fieldOfStudyRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->fieldOfStudyRepository = $fieldOfStudyRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsOfStudyByDepartment(Department $department): array
    {
        return $this->fieldOfStudyRepository->findByDepartment($department);
    }

    /**
     * {@inheritdoc}
     */
    public function saveFieldOfStudy(FieldOfStudy $fieldOfStudy, Department $department): void
    {
        $fieldOfStudy->setDepartment($department);
        $this->entityManager->persist($fieldOfStudy);
        $this->entityManager->flush();
    }
}

