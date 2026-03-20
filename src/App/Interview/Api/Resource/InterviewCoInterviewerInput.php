<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Interview\Api\State\InterviewCoInterviewerProcessor;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/interviews/{id}/co-interviewer',
            processor: InterviewCoInterviewerProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            read: false,
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewCoInterviewerInput
{
    public ?int $userId = null;
}
