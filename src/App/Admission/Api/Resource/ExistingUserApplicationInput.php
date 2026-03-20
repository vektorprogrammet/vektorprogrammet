<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Admission\Api\State\ExistingUserApplicationProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/applications/existing',
            processor: ExistingUserApplicationProcessor::class,
            output: false,
            status: 201,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class ExistingUserApplicationInput
{
    public bool $monday = true;
    public bool $tuesday = true;
    public bool $wednesday = true;
    public bool $thursday = true;
    public bool $friday = true;
    public bool $teamInterest = false;
    public ?string $preferredGroup = null;
}
