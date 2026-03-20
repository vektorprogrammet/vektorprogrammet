<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Content\Api\State\AdminSocialEventDeleteProcessor;
use App\Content\Api\State\AdminSocialEventDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/social-events/{id}',
            provider: AdminSocialEventDeleteProvider::class,
            processor: AdminSocialEventDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminSocialEventDeleteResource
{
    public ?int $id = null;
}
