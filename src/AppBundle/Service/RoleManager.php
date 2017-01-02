<?php

namespace AppBundle\Service;

class RoleManager
{
    const ROLE_ASSISTANT = 'ROLE_USER';
    const ROLE_TEAM_MEMBER = 'ROLE_ADMIN';
    const ROLE_TEAM_LEADER = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_HIGHEST_ADMIN';

    const ROLE_ALIAS_ASSISTANT = 'assistant';
    const ROLE_ALIAS_TEAM_MEMBER = 'team_member';
    const ROLE_ALIAS_TEAM_LEADER = 'team_leader';
    const ROLE_ALIAS_ADMIN = 'admin';

    private $roles = array();
    private $aliases = array();

    public function __construct()
    {
        $this->roles = array(
            $this::ROLE_ASSISTANT,
            $this::ROLE_TEAM_MEMBER,
            $this::ROLE_TEAM_LEADER,
            $this::ROLE_ADMIN,
        );
        $this->aliases = array(
            $this::ROLE_ALIAS_ASSISTANT,
            $this::ROLE_ALIAS_TEAM_MEMBER,
            $this::ROLE_ALIAS_TEAM_LEADER,
            $this::ROLE_ALIAS_ADMIN,
        );
    }

    public function isValidRole(string $role): bool
    {
        return in_array($role, $this->roles) || in_array($role, $this->aliases);
    }

    public function canChangeToRole(string $role): bool
    {
        return
            $role !== $this::ROLE_ADMIN &&
            $role !== $this::ROLE_ALIAS_ADMIN &&
            $this->isValidRole($role)
        ;
    }

    public function mapAliasToRole(string $alias): string
    {
        if (in_array($alias, $this->roles)) {
            return $alias;
        }

        if (in_array($alias, $this->aliases)) {
            return $this->roles[array_search($alias, $this->aliases)];
        }

        return '';
    }
}
