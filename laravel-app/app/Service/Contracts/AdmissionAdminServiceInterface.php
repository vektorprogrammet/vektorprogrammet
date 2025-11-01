<?php

namespace App\Contract;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;

/**
 * Interface for AdmissionAdminService.
 * Defines contract for admission admin operations.
 */
interface AdmissionAdminServiceInterface
{
    /**
     * Get new applications data for display.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function getNewApplicationsData(AdmissionPeriod $admissionPeriod): array;

    /**
     * Get assigned applications data for display.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @param User $currentUser
     * @return array
     */
    public function getAssignedApplicationsData(AdmissionPeriod $admissionPeriod, User $currentUser): array;

    /**
     * Get interviewed applications data for display.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function getInterviewedApplicationsData(AdmissionPeriod $admissionPeriod): array;

    /**
     * Get existing applications data for display.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function getExistingApplicationsData(AdmissionPeriod $admissionPeriod): array;

    /**
     * Delete multiple applications by IDs.
     *
     * @param array $applicationIds
     */
    public function bulkDeleteApplications(array $applicationIds);

    /**
     * Create application from form data.
     *
     * @param Application $application
     * @param AdmissionPeriod $admissionPeriod
     * @param User|null $existingUser
     * @return Application
     */
    public function createApplication(Application $application, AdmissionPeriod $admissionPeriod, ?User $existingUser = null): Application;

    /**
     * Get team interest data.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @param Semester $semester
     * @param Department $department
     * @return array
     */
    public function getTeamInterestData(AdmissionPeriod $admissionPeriod, Semester $semester, Department $department): array;
}


