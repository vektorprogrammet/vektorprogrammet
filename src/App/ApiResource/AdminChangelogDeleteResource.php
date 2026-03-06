<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminChangelogDeleteProcessor;
use App\State\AdminChangelogDeleteProvider;

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
