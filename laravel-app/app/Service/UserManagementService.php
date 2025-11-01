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
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for user management operations.
 * Extracts business logic from UserAdminController.
 */
class UserManagementService implements UserManagementServiceInterface
{
    private $userRepository;
    private $departmentRepository;
    private $roleRepository;
    private $userRegistration;
    private $entityManager;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param UserRegistrationInterface $userRegistration
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        DepartmentRepositoryInterface $departmentRepository,
        RoleRepositoryInterface $roleRepository,
        UserRegistrationInterface $userRegistration,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->departmentRepository = $departmentRepository;
        $this->roleRepository = $roleRepository;
        $this->userRegistration = $userRegistration;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createUserWithDefaults(User $user, Department $department): void
    {
        $role = $this->roleRepository->findByRoleName(Roles::ASSISTANT);
        $user->addRole($role);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

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


