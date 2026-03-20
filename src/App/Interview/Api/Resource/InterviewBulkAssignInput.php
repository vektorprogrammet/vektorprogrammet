<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Interview\Api\State\InterviewBulkAssignProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/interviews/bulk-assign',
            processor: InterviewBulkAssignProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewBulkAssignInput
{
    /** @var array<array{applicationId: int, interviewerId: int, interviewSchemaId: int}> */
    public array $assignments = [];
}
