<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminSchoolDeleteProcessor;
use App\State\AdminSchoolDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/schools/{id}',
            provider: AdminSchoolDeleteProvider::class,
            processor: AdminSchoolDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminSchoolDeleteResource
{
    public ?int $id = null;
}
