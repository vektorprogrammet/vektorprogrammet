<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Interview\Api\State\InterviewDeleteProcessor;
use App\Interview\Api\State\InterviewDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/interviews/{id}',
            provider: InterviewDeleteProvider::class,
            processor: InterviewDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewDeleteResource
{
    public ?int $id = null;
}
