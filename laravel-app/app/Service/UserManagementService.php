<?php

namespace App\Service;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\RoleRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Role\Roles;
use App\Service\Contract\UserManagementServiceInterface;
use App\Service\Contract\UserRegistrationInterface;

/**
 * Service for user management operations.
 * Extracts business logic from UserAdminController.
 */
class UserManagementService implements UserManagementServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRegistrationInterface $userRegistration;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param UserRegistrationInterface $userRegistration
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        DepartmentRepositoryInterface $departmentRepository,
        RoleRepositoryInterface $roleRepository,
        UserRegistrationInterface $userRegistration
    ) {
        $this->userRepository = $userRepository;
        $this->departmentRepository = $departmentRepository;
        $this->roleRepository = $roleRepository;
        $this->userRegistration = $userRegistration;
    }

    /**
     * {@inheritdoc}
     */
    public function createUserWithDefaults(User $user, Department $department): void
    {
        // Save user first so it has an ID for the relationship
        $user->save();

        $role = $this->roleRepository->findByRoleName(Roles::ASSISTANT);
        $user->roles()->attach($role->id);

        $this->userRegistration->sendActivationCode($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredUsersByDepartment(Department $department): array
    {
        $activeDepartments = $this->departmentRepository->findActive();
        $activeUsers = $this->userRepository->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->userRepository->findAllInActiveUsersByDepartment($department);

        return [
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'activeDepartments' => $activeDepartments,
        ];
    }
}


