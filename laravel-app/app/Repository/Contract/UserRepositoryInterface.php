<?php

namespace App\Repository\Contract;

use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface for User repository operations.
 * Extends UserProviderInterface for Symfony security integration.
 * This interface defines the contract for user data access.
 */
interface UserRepositoryInterface extends UserProviderInterface
{
    /**
     * Find users in department with team membership in semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return User[]
     */
    public function findUsersInDepartmentWithTeamMembershipInSemester(
        Department $department,
        Semester $semester
    ): array;

    /**
     * Find users with assistant history in department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return User[]
     */
    public function findUsersWithAssistantHistoryInDepartmentAndSemester(
        Department $department,
        Semester $semester
    ): array;

    /**
     * Find all users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllUsersByDepartment($department): array;

    /**
     * Find all active users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllActiveUsersByDepartment($department): array;

    /**
     * Find all inactive users by department.
     *
     * @param mixed $department Department ID or entity
     * @return User[]
     */
    public function findAllInActiveUsersByDepartment($department): array;

    /**
     * Find all users by department and roles.
     *
     * @param mixed $department Department ID or entity
     * @param array $roles Array of role names
     * @return User[]
     */
    public function findAllUsersByDepartmentAndRoles($department, array $roles): array;

    /**
     * Find all users with receipts.
     *
     * @return User[]
     */
    public function findAllUsersWithReceipts(): array;

    /**
     * Find user by username.
     *
     * @param string $username
     * @return User
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByUsername(string $username): User;

    /**
     * Find user by username, email, or company email.
     *
     * @param string $login Username, email, or company email
     * @return User
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUsernameOrEmail(string $login): User;

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByEmail(string $email): ?User;

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User
     */
    public function findUserById(int $id): User;

    /**
     * Find user by new user code.
     *
     * @param string $code
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByNewUserCode(string $code): ?User;

    /**
     * Find all company emails.
     *
     * @return string[]
     */
    public function findAllCompanyEmails(): array;

    /**
     * Find all assistants (users with assistant history).
     *
     * @return User[]
     */
    public function findAssistants(): array;

    /**
     * Find all team members (users with team memberships).
     *
     * @return User[]
     */
    public function findTeamMembers(): array;
}

