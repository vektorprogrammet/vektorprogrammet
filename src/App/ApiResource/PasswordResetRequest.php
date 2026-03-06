<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\PasswordResetRequestProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password_resets',
            processor: PasswordResetRequestProcessor::class,
            output: false,
            status: 204,
        ),
    ],
)]
class PasswordResetRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';
}
