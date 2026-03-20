<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Identity\Api\State\AdminUserCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/users',
            processor: AdminUserCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
    ],
)]
class AdminUserWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $phone = null;

    public ?int $fieldOfStudyId = null;
}
