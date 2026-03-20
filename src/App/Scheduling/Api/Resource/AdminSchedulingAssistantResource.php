<?php

namespace App\Scheduling\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Scheduling\Api\State\AdminSchedulingAssistantProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/scheduling/assistants',
            provider: AdminSchedulingAssistantProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminSchedulingAssistantResource
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $email = null;

    public ?bool $doublePosition = null;

    public ?int $preferredGroup = null;

    /** @var array<string, bool> */
    public array $availability = [];

    public ?int $score = null;

    public ?string $suitability = null;

    public ?bool $previousParticipation = null;

    public ?string $language = null;
}
