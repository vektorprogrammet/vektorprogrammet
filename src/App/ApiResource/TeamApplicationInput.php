<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\TeamApplicationProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/team_applications',
            processor: TeamApplicationProcessor::class,
            output: false,
            status: 201,
        ),
    ],
)]
class TeamApplicationInput
{
    #[Assert\NotNull]
    public ?int $teamId = null;

    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank]
    public string $phone = '';

    #[Assert\NotBlank]
    public string $fieldOfStudy = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['1. klasse', '2. klasse', '3. klasse', '4. klasse', '5. klasse'])]
    public string $yearOfStudy = '';

    #[Assert\NotBlank]
    public string $motivationText = '';

    #[Assert\NotBlank]
    public string $biography = '';
}
