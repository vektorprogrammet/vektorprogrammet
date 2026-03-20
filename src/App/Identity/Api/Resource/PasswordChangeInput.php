<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Identity\Api\State\PasswordChangeProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/me/password',
            processor: PasswordChangeProcessor::class,
            security: "is_granted('ROLE_USER')",
            output: false,
            status: 204,
        ),
    ],
)]
class PasswordChangeInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 64)]
    public string $newPassword = '';
}
