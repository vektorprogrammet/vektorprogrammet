<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Department;

/**
 * Interface for DepartmentManagementService.
 *
 * Handles business logic for department management operations.
 */
interface DepartmentManagementServiceInterface
{
    /**
     * Create a new department.
     *
     * @param Department $department
     *
     * @return void
     */
    public function createDepartment(Department $department): void;

    /**
     * Update a department.
     *
     * @param Department $department
     *
     * @return void
     */
    public function updateDepartment(Department $department): void;

    /**
     * Delete a department.
     *
     * @param Department $department
     *
     * @return void
     */
    public function deleteDepartment(Department $department): void;
}

