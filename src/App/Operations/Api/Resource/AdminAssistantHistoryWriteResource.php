<?php

namespace App\Operations\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Operations\Api\State\AdminAssistantHistoryCreateProcessor;
use App\Operations\Api\State\AdminAssistantHistoryCreateProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/schools/{id}/assistants',
            provider: AdminAssistantHistoryCreateProvider::class,
            processor: AdminAssistantHistoryCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
    ],
)]
class AdminAssistantHistoryWriteResource
{
    public ?int $schoolId = null;

    #[Assert\NotBlank]
    public ?int $userId = null;

    #[Assert\NotBlank]
    public ?int $semesterId = null;

    #[Assert\NotBlank]
    public ?string $workdays = null;

    #[Assert\NotBlank]
    public ?string $bolk = null;

    #[Assert\NotBlank]
    public ?string $day = null;
}
