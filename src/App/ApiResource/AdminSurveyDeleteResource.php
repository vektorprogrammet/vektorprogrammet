<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminSurveyDeleteProcessor;
use App\State\AdminSurveyDeleteProvider;

#[ApiResource(
    shortName: 'AdminSurveyDelete',
    operations: [
        new Delete(
            uriTemplate: '/admin/surveys/{id}',
            provider: AdminSurveyDeleteProvider::class,
            processor: AdminSurveyDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminSurveyDeleteResource
{
    public ?int $id = null;
}
