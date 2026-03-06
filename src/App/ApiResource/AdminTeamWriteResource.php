<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\AdminTeamCreateProcessor;
use App\State\AdminTeamEditProcessor;
use App\State\AdminTeamEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/teams',
            processor: AdminTeamCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/teams/{id}',
            provider: AdminTeamEditProvider::class,
            processor: AdminTeamEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
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
