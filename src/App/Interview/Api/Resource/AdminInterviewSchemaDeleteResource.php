<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Interview\Api\State\AdminInterviewSchemaDeleteProcessor;
use App\Interview\Api\State\AdminInterviewSchemaDeleteProvider;

#[ApiResource(
    shortName: 'AdminInterviewSchemaDelete',
    operations: [
        new Delete(
            uriTemplate: '/admin/interview-schemas/{id}',
            provider: AdminInterviewSchemaDeleteProvider::class,
            processor: AdminInterviewSchemaDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminInterviewSchemaDeleteResource
{
    public ?int $id = null;
}
