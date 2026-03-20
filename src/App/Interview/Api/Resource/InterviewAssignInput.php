<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Interview\Api\State\InterviewAssignProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/interviews/assign',
            processor: InterviewAssignProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 201,
        ),
    ],
)]
class InterviewAssignInput
{
    #[Assert\Positive]
    public int $applicationId = 0;

    #[Assert\Positive]
    public int $interviewerId = 0;

    #[Assert\Positive]
    public int $interviewSchemaId = 0;
}
