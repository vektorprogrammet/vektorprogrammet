<?php

namespace App\Identity\Domain\Rules;

use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\Entity\User;

class RoleHierarchy
{
    private array $roles = [
        Roles::ASSISTANT,
        Roles::TEAM_MEMBER,
        Roles::TEAM_LEADER,
        Roles::ADMIN,
    ];

    private array $aliases = [
        Roles::ALIAS_ASSISTANT,
        Roles::ALIAS_TEAM_MEMBER,
        Roles::ALIAS_TEAM_LEADER,
        Roles::ALIAS_ADMIN,
    ];

    public function isValidRole(string $role): bool
    {
        return in_array($role, $this->roles, true) || in_array($role, $this->aliases, true);
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
        if (in_array($alias, $this->roles, true)) {
            return $alias;
        }

        $index = array_search($alias, $this->aliases, true);
        if ($index === false) {
            throw new \InvalidArgumentException('Invalid alias: '.$alias);
        }

        return $this->roles[$index];
    }

    public function userIsGranted(User $user, string $role): bool
    {
        $userRoles = $user->getRoles();
        if (count($userRoles) === 0) {
            return false;
        }

        $userRole = $userRoles[0];
        $userAccessLevel = array_search($userRole, $this->roles, true);
        $roleAccessLevel = array_search($role, $this->roles, true);

        if ($userAccessLevel === false || $roleAccessLevel === false) {
            return false;
        }

        return $userAccessLevel >= $roleAccessLevel;
    }
}
