<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdminSurveyNotifierSendProcessor;
use App\State\AdminSurveyNotifierSendProvider;

#[ApiResource(
    shortName: 'AdminSurveyNotifierSend',
    operations: [
        new Post(
            uriTemplate: '/admin/survey-notifiers/{id}/send',
            provider: AdminSurveyNotifierSendProvider::class,
            processor: AdminSurveyNotifierSendProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            deserialize: false,
            status: 200,
        ),
    ],
)]
class AdminSurveyNotifierSendResource
{
    public ?int $id = null;
    public ?bool $success = null;
}
