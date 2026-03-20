<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Identity\Api\State\AdminUserDeleteProcessor;
use App\Identity\Api\State\AdminUserDeleteProvider;

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
