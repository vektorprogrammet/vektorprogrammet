<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\AdminChangelogCreateProcessor;
use App\State\AdminChangelogEditProcessor;
use App\State\AdminChangelogEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/changelogs',
            processor: AdminChangelogCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/changelogs/{id}',
            provider: AdminChangelogEditProvider::class,
            processor: AdminChangelogEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminChangelogWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    public ?string $title = null;

    public ?string $description = null;

    public ?string $date = null;

    public ?string $githubLink = null;
}
