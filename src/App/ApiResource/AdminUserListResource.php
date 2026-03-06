<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AdminUserListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/users',
            provider: AdminUserListProvider::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminUserListResource
{
    public array $activeUsers = [];

    public array $inactiveUsers = [];

    public string $departmentName = '';
}
