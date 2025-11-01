<?php

namespace App\Contract;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\Team;

/**
 * Interface for AdmissionService.
 * Defines contract for admission page preparation and application submission.
 */
interface AdmissionServiceInterface
{
    /**
     * Prepare admission page data.
     *
     * @param Department|null $specificDepartment
     * @return array Contains 'departments', 'departmentsWithActiveAdmission', 'teams', etc.
     */
    public function prepareAdmissionPageData(Department $specificDepartment = null): array;

    /**
     * Submit an application.
     *
     * @param Application $application
     * @param Department $department
     * @return array Contains 'application', 'admissionPeriod', 'redirectRoute' or 'error'
     */
    public function submitApplication(Application $application, Department $department): array;

    /**
     * Find department by city (case-insensitive).
     *
     * @param string $city
     * @return Department|null
     */
    public function findDepartmentByCity(string $city): ?Department;

    /**
     * Get teams for department that have open applications.
     *
     * @param Department $department
     * @return array
     */
    public function getTeamsForDepartment(Department $department): array;

    /**
     * Validate and get active admission period for department.
     *
     * @param Department $department
     * @return AdmissionPeriod|null
     */
    public function validateAdmissionPeriod(Department $department): ?AdmissionPeriod;
}

