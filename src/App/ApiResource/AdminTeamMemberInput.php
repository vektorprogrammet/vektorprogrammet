<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdminTeamMemberAddProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/teams/{id}/members',
            processor: AdminTeamMemberAddProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
    ],
)]
class AdminTeamMemberInput
{
    #[Assert\NotNull]
    public ?int $userId = null;

    public ?int $positionId = null;

    #[Assert\NotNull]
    public ?int $startSemesterId = null;

    public ?int $endSemesterId = null;
}
