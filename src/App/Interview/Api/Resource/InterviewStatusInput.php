<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Interview\Api\State\InterviewStatusProcessor;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/interviews/{id}/status',
            processor: InterviewStatusProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            read: false,
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewStatusInput
{
    public int $status = 0;
}
