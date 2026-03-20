<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Admission\Api\State\AdminApplicationDeleteProcessor;
use App\Admission\Api\State\AdminApplicationDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/applications/{id}',
            provider: AdminApplicationDeleteProvider::class,
            processor: AdminApplicationDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminApplicationDeleteResource
{
    public ?int $id = null;
}
