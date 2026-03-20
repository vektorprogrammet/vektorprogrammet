<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Interview\Api\State\InterviewClearCoInterviewerProcessor;
use App\Interview\Api\State\InterviewClearCoInterviewerProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/interviews/{id}/co-interviewer',
            provider: InterviewClearCoInterviewerProvider::class,
            processor: InterviewClearCoInterviewerProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewClearCoInterviewerResource
{
    public ?int $id = null;
}
