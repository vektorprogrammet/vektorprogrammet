<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Content\Api\State\AdminSocialEventCreateProcessor;
use App\Content\Api\State\AdminSocialEventEditProcessor;
use App\Content\Api\State\AdminSocialEventEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/social-events',
            processor: AdminSocialEventCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/social-events/{id}',
            provider: AdminSocialEventEditProvider::class,
            processor: AdminSocialEventEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminSocialEventWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    public ?string $description = null;

    #[Assert\NotBlank]
    public ?string $startTime = null;

    #[Assert\NotBlank]
    public ?string $endTime = null;

    public ?int $departmentId = null;

    public ?int $semesterId = null;

    public ?string $link = null;
}
