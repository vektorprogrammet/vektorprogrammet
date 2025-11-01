<?php

namespace App\Contract;

use App\Models\AdmissionPeriod;
use App\Models\Department;

/**
 * Interface for admission period validation logic.
 */
interface AdmissionPeriodValidationServiceInterface
{
    /**
     * Check if an admission period with the same semester already exists for a department.
     *
     * @param AdmissionPeriod $admissionPeriod The admission period to check
     * @param Department $department The department
     * @return bool True if a duplicate exists, false otherwise
     */
    public function semesterExistsForDepartment(AdmissionPeriod $admissionPeriod, Department $department): bool;
}

