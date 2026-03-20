<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Organization\Api\State\TeamInterestProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/team-interest',
            provider: TeamInterestProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class TeamInterestResource
{
    public array $applicants = [];

    public array $teams = [];
}
