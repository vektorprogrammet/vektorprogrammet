<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Organization\Api\State\AdminTeamCreateProcessor;
use App\Organization\Api\State\AdminTeamEditProcessor;
use App\Organization\Api\State\AdminTeamEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/teams',
            processor: AdminTeamCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/teams/{id}',
            provider: AdminTeamEditProvider::class,
            processor: AdminTeamEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminTeamWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public string $name = '';

    public ?string $email = null;

    public ?string $shortDescription = null;

    public ?string $description = null;

    public ?int $departmentId = null;
}
