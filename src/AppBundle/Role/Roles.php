<?php

namespace AppBundle\Role;

class Roles
{
    const ASSISTANT = 'ROLE_USER';
    const TEAM_MEMBER = 'ROLE_ADMIN';
    const TEAM_LEADER = 'ROLE_SUPER_ADMIN';
    const ADMIN = 'ROLE_HIGHEST_ADMIN';

    const ALIAS_ASSISTANT = 'assistant';
    const ALIAS_TEAM_MEMBER = 'team_member';
    const ALIAS_TEAM_LEADER = 'team_leader';
    const ALIAS_ADMIN = 'admin';
}
