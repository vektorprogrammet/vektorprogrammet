<?php

namespace App\Contract;

use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\School;
use App\Models\User;

/**
 * Interface for SchoolManagementService.
 *
 * Handles business logic for school administration, including data aggregation,
 * school-assistant relationship management, and school lifecycle operations.
 */
interface SchoolManagementServiceInterface
{
    /**
     * Get schools data for a department (active and inactive).
     *
     * @param Department $department
     *
     * @return array{activeSchools: array, inactiveSchools: array}
     */
    public function getSchoolsData(Department $department): array;

    /**
     * Get assistant history data for a specific school.
     *
     * @param School $school
     *
     * @return array{activeAssistantHistories: array, inactiveAssistantHistories: array}
     */
    public function getAssistantHistoryData(School $school): array;

    /**
     * Get users data for a department.
     *
     * @param Department|null $department If null, department is derived from current user's field of study
     * @param User $currentUser
     *
     * @return array{departments: array, department: Department, users: array}
     */
    public function getUsersData(?Department $department, User $currentUser): array;

    /**
     * Create an assistant history for a user.
     *
     * @param AssistantHistory $assistantHistory
     * @param User $user
     *
     * @return void
     */
    public function createAssistantHistory(AssistantHistory $assistantHistory, User $user): void;

    /**
     * Create a school and associate it with a department.
     *
     * @param School $school
     * @param Department $department
     *
     * @return void
     */
    public function createSchoolForDepartment(School $school, Department $department): void;

    /**
     * Delete a school.
     *
     * @param School $school
     *
     * @return void
     */
    public function deleteSchool(School $school): void;

    /**
     * Remove user from school by deleting assistant history.
     *
     * @param AssistantHistory $assistantHistory
     *
     * @return void
     */
    public function removeUserFromSchool(AssistantHistory $assistantHistory): void;
}

