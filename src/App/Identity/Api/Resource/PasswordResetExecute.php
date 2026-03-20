<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Identity\Api\State\PasswordResetExecuteProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/password_resets/{code}',
            processor: PasswordResetExecuteProcessor::class,
            output: false,
            status: 204,
        ),
    ],
)]
class PasswordResetExecute
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password = '';
}
