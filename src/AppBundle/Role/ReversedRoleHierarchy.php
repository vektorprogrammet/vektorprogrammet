<?php

namespace AppBundle\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

/**
 * ReversedRoleHierarchy defines a reversed role hierarchy.
 */
class ReversedRoleHierarchy extends RoleHierarchy
{
    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     */
    public function __construct(array $hierarchy)
    {
        // Reverse the role hierarchy.
        $reversed = [];
        foreach ($hierarchy as $main => $roles) {
            foreach ($roles as $role) {
                $reversed[$role][] = $main;
            }
        }

        // Use the original algorithm to build the role map.
        parent::__construct($reversed);
    }

    /**
     * Helper function to get an array of strings.
     *
     * @param array $roleNames An array of string role names
     *
     * @return array An array of string role names
     */
    public function getParentRoles(array $roleNames)
    {
        $roles = [];
        foreach ($roleNames as $roleName) {
            $roles[] = new Role($roleName);
        }

        $results = [];
        foreach ($this->getReachableRoles($roles) as $parent) {
            $results[] = $parent->getRole();
        }

        return $results;
    }
}
