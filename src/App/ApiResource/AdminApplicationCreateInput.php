<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdminApplicationCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/applications',
            processor: AdminApplicationCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
    ],
)]
class AdminApplicationCreateInput
{
    #[Assert\NotBlank]
    public string $firstName = '';

    #[Assert\NotBlank]
    public string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    public string $phone = '';

    #[Assert\NotNull]
    public ?int $fieldOfStudyId = null;

    #[Assert\NotNull]
    public ?int $admissionPeriodId = null;

    public string $yearOfStudy = '1. klasse';
}
