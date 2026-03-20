<?php

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Organization\Api\State\AdminFieldOfStudyCreateProcessor;
use App\Organization\Api\State\AdminFieldOfStudyEditProcessor;
use App\Organization\Api\State\AdminFieldOfStudyEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/field-of-studies',
            processor: AdminFieldOfStudyCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/field-of-studies/{id}',
            provider: AdminFieldOfStudyEditProvider::class,
            processor: AdminFieldOfStudyEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminFieldOfStudyWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $shortName = null;
}
