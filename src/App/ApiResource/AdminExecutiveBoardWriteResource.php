<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\State\AdminExecutiveBoardEditProcessor;
use App\State\AdminExecutiveBoardEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/executive-board',
            provider: AdminExecutiveBoardEditProvider::class,
            processor: AdminExecutiveBoardEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminExecutiveBoardWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public string $name = '';

    public ?string $description = null;

    public ?string $shortDescription = null;
}
