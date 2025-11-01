<?php

namespace App\Repository\Contract;

use App\Models\Role;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for Role repository operations.
 * This interface defines the contract for role data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface RoleRepositoryInterface
{
    /**
     * Find role by role name.
     *
     * @param string $roleName
     * @return Role
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findByRoleName(string $roleName): Role;
}

