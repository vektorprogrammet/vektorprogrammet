<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminTeamDeleteProcessor;
use App\State\AdminTeamDeleteProvider;

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
