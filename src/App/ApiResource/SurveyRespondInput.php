<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\SurveyRespondProcessor;

/**
 * Survey response input DTO.
 *
 * security: 'true' is intentionally permissive because school surveys (targetAudience=0)
 * are anonymous and require no authentication. Authentication for team and assistant
 * surveys is enforced in the processor, which throws UnauthorizedHttpException when
 * a user token is required but missing.
 */
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/surveys/{id}/respond',
        processor: SurveyRespondProcessor::class,
        security: 'true',
        output: false,
        status: 204,
    ),
])]
class SurveyRespondInput
{
    /** @var array<array{questionId: int, answer: string|string[]}> */
    public array $answers = [];
}
