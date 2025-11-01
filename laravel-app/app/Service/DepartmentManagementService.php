<?php

namespace App\Service;

use App\Models\Department;
use App\Service\Contract\DepartmentManagementServiceInterface;

/**
 * Service for managing departments and department-related business logic.
 */
class DepartmentManagementService implements DepartmentManagementServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createDepartment(Department $department): void
    {
        $department->save();
    }

    /**
     * {@inheritdoc}
     */
    public function updateDepartment(Department $department): void
    {
        $department->save();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDepartment(Department $department): void
    {
        $department->delete();
    }
}

