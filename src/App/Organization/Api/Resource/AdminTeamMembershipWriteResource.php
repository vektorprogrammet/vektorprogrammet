<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Organization\Api\State\AdminTeamMembershipEditProcessor;
use App\Organization\Api\State\AdminTeamMembershipEditProvider;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/team-memberships/{id}',
            provider: AdminTeamMembershipEditProvider::class,
            processor: AdminTeamMembershipEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
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
