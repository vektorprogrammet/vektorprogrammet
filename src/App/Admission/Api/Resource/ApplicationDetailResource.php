<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Admission\Api\State\ApplicationDetailProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/applications/{id}',
            provider: ApplicationDetailProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class ApplicationDetailResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    public ?string $userName = null;

    public ?string $userEmail = null;

    public ?string $userPhone = null;

    public ?bool $previousParticipation = null;

    public ?string $yearOfStudy = null;

    public ?bool $monday = null;

    public ?bool $tuesday = null;

    public ?bool $wednesday = null;

    public ?bool $thursday = null;

    public ?bool $friday = null;

    public ?array $heardAboutFrom = null;

    public ?string $language = null;

    public ?string $preferredGroup = null;

    public ?bool $doublePosition = null;

    public ?bool $teamInterest = null;

    public ?bool $substitute = null;

    public ?string $interviewScheduled = null;

    public ?string $interviewStatus = null;

    public ?string $created = null;
}
