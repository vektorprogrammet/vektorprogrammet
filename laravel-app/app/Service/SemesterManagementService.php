<?php

namespace App\Service;

use App\Models\Semester;
use App\Service\Contract\SemesterManagementServiceInterface;
use App\Service\Contract\SemesterValidationServiceInterface;

/**
 * Service for managing semesters and semester-related business logic.
 */
class SemesterManagementService implements SemesterManagementServiceInterface
{
    private SemesterValidationServiceInterface $semesterValidationService;

    /**
     * @param SemesterValidationServiceInterface $semesterValidationService
     */
    public function __construct(
        SemesterValidationServiceInterface $semesterValidationService
    ) {
        $this->semesterValidationService = $semesterValidationService;
    }

    /**
     * {@inheritdoc}
     */
    public function createSemester(Semester $semester): array
    {
        $existingSemester = $this->semesterValidationService->findExistingSemester(
            $semester->semester_time,
            $semester->year
        );

        if ($existingSemester !== null) {
            return [
                'success' => false,
                'existingSemester' => $existingSemester,
            ];
        }

        $semester->save();

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
        $semester->delete();
    }
}

