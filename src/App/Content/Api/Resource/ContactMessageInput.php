<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Content\Api\State\ContactMessageProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/contact_messages',
            processor: ContactMessageProcessor::class,
            output: false,
            status: 201,
        ),
    ],
)]
class ContactMessageInput
{
    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotNull]
    public ?int $departmentId = null;

    #[Assert\NotBlank]
    public string $subject = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 5000)]
    public string $message = '';
}
