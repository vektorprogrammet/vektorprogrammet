<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\State\ProfileProcessor;
use App\State\ProfileProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            provider: ProfileProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
        new Put(
            uriTemplate: '/me',
            provider: ProfileProvider::class,
            processor: ProfileProcessor::class,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class ProfileResource
{
    public ?int $id = null;

    #[Assert\NotBlank]
    public string $firstName = '';

    #[Assert\NotBlank]
    public string $lastName = '';

    public ?string $userName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    public ?string $phone = null;

    public ?int $gender = null;

    public ?array $fieldOfStudy = null;

    public string $role = 'ROLE_USER';

    public ?string $profilePhoto = null;
}
