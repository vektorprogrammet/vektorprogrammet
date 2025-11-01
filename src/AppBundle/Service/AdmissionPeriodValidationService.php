<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Service\Contract\AdmissionPeriodValidationServiceInterface;

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

