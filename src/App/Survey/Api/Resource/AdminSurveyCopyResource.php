<?php

namespace App\Survey\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Survey\Api\State\AdminSurveyCopyProcessor;
use App\Survey\Api\State\AdminSurveyCopyProvider;

#[ApiResource(
    shortName: 'AdminSurveyCopy',
    operations: [
        new Post(
            uriTemplate: '/admin/surveys/{id}/copy',
            provider: AdminSurveyCopyProvider::class,
            processor: AdminSurveyCopyProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
            deserialize: false,
        ),
    ],
)]
class AdminSurveyCopyResource
{
    public ?int $id = null;
}
