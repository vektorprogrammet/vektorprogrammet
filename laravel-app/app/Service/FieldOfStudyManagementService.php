<?php

namespace App\Service;

use App\Models\Department;
use App\Models\FieldOfStudy;
use App\Repository\Contract\FieldOfStudyRepositoryInterface;
use App\Service\Contract\FieldOfStudyManagementServiceInterface;

/**
 * Service for managing fields of study and field of study-related business logic.
 */
class FieldOfStudyManagementService implements FieldOfStudyManagementServiceInterface
{
    private FieldOfStudyRepositoryInterface $fieldOfStudyRepository;

    /**
     * @param FieldOfStudyRepositoryInterface $fieldOfStudyRepository
     */
    public function __construct(
        FieldOfStudyRepositoryInterface $fieldOfStudyRepository
    ) {
        $this->fieldOfStudyRepository = $fieldOfStudyRepository;
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
        $fieldOfStudy->department_id = $department->id;
        $fieldOfStudy->save();
    }
}

