<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\TeamInterestProvider;

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
