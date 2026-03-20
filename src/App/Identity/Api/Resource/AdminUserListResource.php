<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Identity\Api\State\AdminUserListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/users',
            provider: AdminUserListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminUserListResource
{
    public array $activeUsers = [];

    public array $inactiveUsers = [];

    public string $departmentName = '';
}
