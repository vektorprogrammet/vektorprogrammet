<?php

namespace App\Contract;

use App\Models\Semester;

/**
 * Interface for SemesterManagementService.
 *
 * Handles business logic for semester management, including validation and lifecycle operations.
 */
interface SemesterManagementServiceInterface
{
    /**
     * Create a semester with validation.
     *
     * @param Semester $semester
     *
     * @return array{success: bool, existingSemester?: Semester|null} Returns success status and existing semester if found
     */
    public function createSemester(Semester $semester): array;

    /**
     * Delete a semester.
     *
     * @param Semester $semester
     *
     * @return void
     */
    public function deleteSemester(Semester $semester): void;
}

