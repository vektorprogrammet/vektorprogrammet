<?php

namespace App\Contract;

use App\Models\Department;
use App\Models\FieldOfStudy;

/**
 * Interface for FieldOfStudyManagementService.
 *
 * Handles business logic for field of study management operations.
 */
interface FieldOfStudyManagementServiceInterface
{
    /**
     * Get fields of study for a department.
     *
     * @param Department $department
     *
     * @return array
     */
    public function getFieldsOfStudyByDepartment(Department $department): array;

    /**
     * Create or update a field of study.
     *
     * @param FieldOfStudy $fieldOfStudy
     * @param Department $department
     *
     * @return void
     */
    public function saveFieldOfStudy(FieldOfStudy $fieldOfStudy, Department $department): void;
}

