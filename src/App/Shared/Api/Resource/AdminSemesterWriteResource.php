<?php

namespace App\Shared\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Shared\Api\State\AdminSemesterCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/semesters',
            processor: AdminSemesterCreateProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            status: 201,
        ),
    ],
)]
class AdminSemesterWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['Vår', 'Høst'], message: 'Must be "Vår" or "Høst".')]
    public ?string $semesterTime = null;

    #[Assert\NotBlank]
    public ?string $year = null;
}
