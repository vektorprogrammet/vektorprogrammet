<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\RoleRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;

/**
 * Interface for UserManagement service operations.
 * Defines contract for user creation and data filtering.
 */
interface UserManagementServiceInterface
{
    /**
     * Create a new user with default assistant role.
     * Persists the user and sends activation code.
     *
     * @param User $user
     * @param Department $department
     * @return void
     */
    public function createUserWithDefaults(User $user, Department $department): void;

    /**
     * Get filtered users by department with active/inactive separation.
     *
     * @param Department $department
     * @return array Data with keys:
     *   - activeUsers: User[]
     *   - inActiveUsers: User[]
     *   - activeDepartments: Department[]
     */
    public function getFilteredUsersByDepartment(Department $department): array;
}


