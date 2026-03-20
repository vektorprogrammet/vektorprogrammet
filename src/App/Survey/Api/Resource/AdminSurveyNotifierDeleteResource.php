<?php

namespace App\Survey\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Survey\Api\State\AdminSurveyNotifierDeleteProcessor;
use App\Survey\Api\State\AdminSurveyNotifierDeleteProvider;

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
