<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\ApplicationProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/applications',
            processor: ApplicationProcessor::class,
            output: false,
            status: 201,
        ),
    ],
)]
class ApplicationInput
{
    #[Assert\NotBlank]
    public string $firstName = '';

    #[Assert\NotBlank]
    public string $lastName = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank]
    public string $phone = '';

    #[Assert\NotNull]
    public ?int $fieldOfStudyId = null;

    #[Assert\NotBlank]
    public string $yearOfStudy = '';

    #[Assert\NotNull]
    #[Assert\Choice(choices: [0, 1])]
    public ?int $gender = null;

    #[Assert\NotNull]
    public ?int $departmentId = null;

    public bool $monday = true;
    public bool $tuesday = true;
    public bool $wednesday = true;
    public bool $thursday = true;
    public bool $friday = true;
    public bool $substitute = false;
    public string $language = 'Norsk';
    public bool $doublePosition = false;
    public ?string $preferredSchool = null;
    public ?string $preferredGroup = null;
    public bool $previousParticipation = false;
    public bool $teamInterest = false;
    public ?string $specialNeeds = null;
}
