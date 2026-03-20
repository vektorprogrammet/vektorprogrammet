<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Content\Api\State\AdminChangelogDeleteProcessor;
use App\Content\Api\State\AdminChangelogDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/changelogs/{id}',
            provider: AdminChangelogDeleteProvider::class,
            processor: AdminChangelogDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminChangelogDeleteResource
{
    public ?int $id = null;
}
