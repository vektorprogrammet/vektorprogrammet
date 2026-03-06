<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\AdminSchedulingSchoolProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/scheduling/schools',
            provider: AdminSchedulingSchoolProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminSchedulingSchoolResource
{
    public ?int $id = null;

    public ?string $name = null;

    /** @var array<int, array<string, int>> */
    public array $capacity = [];
}
