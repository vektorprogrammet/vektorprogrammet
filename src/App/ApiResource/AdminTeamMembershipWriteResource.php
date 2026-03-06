<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\State\AdminTeamMembershipEditProcessor;
use App\State\AdminTeamMembershipEditProvider;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/team-memberships/{id}',
            provider: AdminTeamMembershipEditProvider::class,
            processor: AdminTeamMembershipEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminTeamMembershipWriteResource
{
    public ?int $id = null;

    public ?int $positionId = null;

    public ?int $startSemesterId = null;

    public ?int $endSemesterId = null;
}
