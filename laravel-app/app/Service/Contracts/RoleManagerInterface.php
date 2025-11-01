<?php

namespace App\Contract;

use App\Models\User;

/**
 * Interface for RoleManager service.
 * Defines contract for role management operations.
 */
interface RoleManagerInterface
{
    /**
     * Check if a role is valid.
     *
     * @param string $role
     * @return bool
     */
    public function isValidRole(string $role): bool;

    /**
     * Check if user can change to a role.
     *
     * @param string $role
     * @return bool
     */
    public function canChangeToRole(string $role): bool;

    /**
     * Map alias to role.
     *
     * @param string $alias
     * @return string
     */
    public function mapAliasToRole(string $alias): string;

    /**
     * Check if logged-in user can create user with role.
     *
     * @param string $role
     * @return bool
     */
    public function loggedInUserCanCreateUserWithRole(string $role): bool;

    /**
     * Check if logged-in user can change role of users with role.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function loggedInUserCanChangeRoleOfUsersWithRole(User $user, string $role): bool;

    /**
     * Check if user is granted a role.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function userIsGranted(User $user, string $role): bool;

    /**
     * Update user role based on their current status.
     *
     * @param User $user
     * @return bool True if role was updated, false if no role changed
     */
    public function updateUserRole(User $user);

    /**
     * Check if user is in executive board.
     *
     * @param User $user
     * @return bool
     */
    public function userIsInExecutiveBoard(User $user);
}
