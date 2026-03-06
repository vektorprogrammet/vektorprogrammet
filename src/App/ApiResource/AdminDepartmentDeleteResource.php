<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminDepartmentDeleteProcessor;
use App\State\AdminDepartmentDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/departments/{id}',
            provider: AdminDepartmentDeleteProvider::class,
            processor: AdminDepartmentDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminDepartmentDeleteResource
{
    public ?int $id = null;
}
