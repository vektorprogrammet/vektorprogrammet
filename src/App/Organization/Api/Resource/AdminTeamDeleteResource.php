<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Organization\Api\State\AdminTeamDeleteProcessor;
use App\Organization\Api\State\AdminTeamDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/teams/{id}',
            provider: AdminTeamDeleteProvider::class,
            processor: AdminTeamDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminTeamDeleteResource
{
    public ?int $id = null;
}
