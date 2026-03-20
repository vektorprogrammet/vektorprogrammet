<?php

namespace App\Identity\Domain\Rules;

use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\Entity\User;

class RoleHierarchy
{
    private const ROLES = [
        Roles::ASSISTANT,
        Roles::TEAM_MEMBER,
        Roles::TEAM_LEADER,
        Roles::ADMIN,
    ];

    private const ALIASES = [
        Roles::ALIAS_ASSISTANT,
        Roles::ALIAS_TEAM_MEMBER,
        Roles::ALIAS_TEAM_LEADER,
        Roles::ALIAS_ADMIN,
    ];

    public function isValidRole(string $role): bool
    {
        return in_array($role, self::ROLES, true) || in_array($role, self::ALIASES, true);
    }

    public function canChangeToRole(string $role): bool
    {
        return
            $role !== Roles::ADMIN
            && $role !== Roles::ALIAS_ADMIN
            && $this->isValidRole($role)
        ;
    }

    public function mapAliasToRole(string $alias): string
    {
        if (in_array($alias, self::ROLES, true)) {
            return $alias;
        }

        $index = array_search($alias, self::ALIASES, true);
        if ($index === false) {
            throw new \InvalidArgumentException('Invalid alias: '.$alias);
        }

        return self::ROLES[$index];
    }

    public function userIsGranted(User $user, string $role): bool
    {
        $userRoles = $user->getRoles();
        if (count($userRoles) === 0) {
            return false;
        }

        $userRole = $userRoles[0];
        $userAccessLevel = array_search($userRole, self::ROLES, true);
        $roleAccessLevel = array_search($role, self::ROLES, true);

        if ($userAccessLevel === false || $roleAccessLevel === false) {
            return false;
        }

        return $userAccessLevel >= $roleAccessLevel;
    }
}
