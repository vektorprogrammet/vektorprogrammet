<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminTeamMembershipDeleteProcessor;
use App\State\AdminTeamMembershipDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/team-memberships/{id}',
            provider: AdminTeamMembershipDeleteProvider::class,
            processor: AdminTeamMembershipDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminTeamMembershipDeleteResource
{
    public ?int $id = null;
}
