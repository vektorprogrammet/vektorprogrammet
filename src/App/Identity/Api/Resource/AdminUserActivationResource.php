<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Identity\Api\State\AdminUserActivationProcessor;
use App\Identity\Api\State\AdminUserActivationProvider;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/users/{id}/activation',
            provider: AdminUserActivationProvider::class,
            processor: AdminUserActivationProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 200,
        ),
    ],
)]
class AdminUserActivationResource
{
    public ?int $id = null;
}
