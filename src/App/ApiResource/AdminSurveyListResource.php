<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AdminSurveyListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/surveys',
            provider: AdminSurveyListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminSurveyListResource
{
    public array $surveys = [];
}
