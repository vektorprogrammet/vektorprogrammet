<?php

namespace App\Repository\Eloquent;

use App\Models\Role;
use App\Repository\Contract\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of RoleRepositoryInterface.
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Find role by role name.
     *
     * @param string $roleName
     * @return Role
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByRoleName(string $roleName): Role
    {
        try {
            return Role::where('role', $roleName)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Doctrine\ORM\NoResultException('No role found with name: ' . $roleName);
        }
    }
}

