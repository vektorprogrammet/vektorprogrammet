<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Service\Contract\AdmissionPeriodValidationServiceInterface;

/**
 * Service for admission period validation logic.
 */
class AdmissionPeriodValidationService implements AdmissionPeriodValidationServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function semesterExistsForDepartment(AdmissionPeriod $admissionPeriod, Department $department): bool
    {
        return $department->getAdmissionPeriods()->exists(function ($key, $value) use ($admissionPeriod) {
            return $value->getSemester() === $admissionPeriod->getSemester();
        });
    }
}

