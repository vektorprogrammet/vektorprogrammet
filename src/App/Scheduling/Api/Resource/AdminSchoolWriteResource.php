<?php

namespace App\Scheduling\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Scheduling\Api\State\AdminSchoolCreateProcessor;
use App\Scheduling\Api\State\AdminSchoolEditProcessor;
use App\Scheduling\Api\State\AdminSchoolEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/schools',
            processor: AdminSchoolCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/schools/{id}',
            provider: AdminSchoolEditProvider::class,
            processor: AdminSchoolEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminSchoolWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $contactPerson = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $phone = null;

    public bool $international = false;

    public bool $active = true;

    public ?int $departmentId = null;
}
