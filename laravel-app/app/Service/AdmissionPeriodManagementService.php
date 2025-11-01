<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Service\Contract\AdmissionPeriodManagementServiceInterface;
use App\Service\Contract\AdmissionPeriodValidationServiceInterface;

/**
 * Service for managing admission periods and admission period-related business logic.
 */
class AdmissionPeriodManagementService implements AdmissionPeriodManagementServiceInterface
{
    private AdmissionPeriodValidationServiceInterface $admissionPeriodValidationService;

    /**
     * @param AdmissionPeriodValidationServiceInterface $admissionPeriodValidationService
     */
    public function __construct(
        AdmissionPeriodValidationServiceInterface $admissionPeriodValidationService
    ) {
        $this->admissionPeriodValidationService = $admissionPeriodValidationService;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdmissionPeriod(AdmissionPeriod $admissionPeriod, Department $department): array
    {
        $exists = $this->admissionPeriodValidationService->semesterExistsForDepartment($admissionPeriod, $department);

        if ($exists) {
            return [
                'success' => false,
                'exists' => true,
            ];
        }

        $admissionPeriod->department_id = $department->id;
        $admissionPeriod->save();

        return [
            'success' => true,
            'exists' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateAdmissionPeriod(AdmissionPeriod $admissionPeriod): void
    {
        $admissionPeriod->save();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAdmissionPeriod(AdmissionPeriod $admissionPeriod): void
    {
        $infoMeeting = $admissionPeriod->infoMeeting ?? null;
        if ($infoMeeting !== null) {
            $infoMeeting->delete();
        }
        $admissionPeriod->delete();
    }
}

