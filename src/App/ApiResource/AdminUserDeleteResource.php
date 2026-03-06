<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminUserDeleteProcessor;
use App\State\AdminUserDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/users/{id}',
            provider: AdminUserDeleteProvider::class,
            processor: AdminUserDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminUserDeleteResource
{
    public ?int $id = null;
}
