<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;

/**
 * Interface for AdmissionPeriodManagementService.
 *
 * Handles business logic for admission period management, including validation and lifecycle operations.
 */
interface AdmissionPeriodManagementServiceInterface
{
    /**
     * Create an admission period with validation.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @param Department $department
     *
     * @return array{success: bool, exists: bool} Returns success status and whether period already exists
     */
    public function createAdmissionPeriod(AdmissionPeriod $admissionPeriod, Department $department): array;

    /**
     * Update an admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return void
     */
    public function updateAdmissionPeriod(AdmissionPeriod $admissionPeriod): void;

    /**
     * Delete an admission period and its associated info meeting.
     *
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return void
     */
    public function deleteAdmissionPeriod(AdmissionPeriod $admissionPeriod): void;
}

