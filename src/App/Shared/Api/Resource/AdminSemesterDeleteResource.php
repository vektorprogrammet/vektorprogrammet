<?php

namespace App\Shared\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Shared\Api\State\AdminSemesterDeleteProcessor;
use App\Shared\Api\State\AdminSemesterDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/semesters/{id}',
            provider: AdminSemesterDeleteProvider::class,
            processor: AdminSemesterDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminSemesterDeleteResource
{
    public ?int $id = null;
}
