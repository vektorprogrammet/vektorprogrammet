<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\InterviewConductProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/interviews/{id}/conduct',
            read: false,
            processor: InterviewConductProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewConductInput
{
    /** @var array<array{questionId: int, answer: mixed}> */
    public array $answers = [];

    /** @var array{explanatoryPower: int, roleModel: int, suitability: int, suitableAssistant: string} */
    public array $interviewScore = [];
}
