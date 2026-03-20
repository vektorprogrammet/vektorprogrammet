<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Admission\Api\State\AdmissionSubscriberProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admission_subscribers',
            processor: AdmissionSubscriberProcessor::class,
            output: false,
            status: 201,
        ),
    ],
)]
class AdmissionSubscriberInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotNull]
    public ?int $departmentId = null;

    public bool $infoMeeting = false;
}
