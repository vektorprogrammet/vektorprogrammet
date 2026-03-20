<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Organization\Api\State\AdminDepartmentCreateProcessor;
use App\Organization\Api\State\AdminDepartmentEditProcessor;
use App\Organization\Api\State\AdminDepartmentEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/departments',
            processor: AdminDepartmentCreateProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/departments/{id}',
            provider: AdminDepartmentEditProvider::class,
            processor: AdminDepartmentEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminDepartmentWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $shortName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $city = null;

    public ?string $address = null;

    public ?string $latitude = null;

    public ?string $longitude = null;
}
