<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminInterviewSchemaDeleteProcessor;
use App\State\AdminInterviewSchemaDeleteProvider;

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
