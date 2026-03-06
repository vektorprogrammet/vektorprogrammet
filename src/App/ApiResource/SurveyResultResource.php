<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\SurveyResultProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/survey-results/{id}',
            provider: SurveyResultProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class SurveyResultResource
{
    public int $id = 0;

    public array $survey = [];

    public array $answers = [];
}
