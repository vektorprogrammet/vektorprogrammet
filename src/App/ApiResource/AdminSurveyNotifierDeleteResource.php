<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminSurveyNotifierDeleteProcessor;
use App\State\AdminSurveyNotifierDeleteProvider;

#[ApiResource(
    shortName: 'AdminSurveyNotifierDelete',
    operations: [
        new Delete(
            uriTemplate: '/admin/survey-notifiers/{id}',
            provider: AdminSurveyNotifierDeleteProvider::class,
            processor: AdminSurveyNotifierDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminSurveyNotifierDeleteResource
{
    public ?int $id = null;
}
