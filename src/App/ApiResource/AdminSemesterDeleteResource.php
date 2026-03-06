<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminSemesterDeleteProcessor;
use App\State\AdminSemesterDeleteProvider;

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
