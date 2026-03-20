<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Organization\Api\State\AdminTeamMembershipDeleteProcessor;
use App\Organization\Api\State\AdminTeamMembershipDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/team-memberships/{id}',
            provider: AdminTeamMembershipDeleteProvider::class,
            processor: AdminTeamMembershipDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminTeamMembershipDeleteResource
{
    public ?int $id = null;
}
