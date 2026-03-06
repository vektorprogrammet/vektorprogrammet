<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdminUserActivationProcessor;
use App\State\AdminUserActivationProvider;

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
