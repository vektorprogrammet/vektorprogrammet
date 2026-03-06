<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\InterviewClearCoInterviewerProcessor;
use App\State\InterviewClearCoInterviewerProvider;

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
