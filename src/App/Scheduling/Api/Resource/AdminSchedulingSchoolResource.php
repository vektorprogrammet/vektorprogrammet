<?php

namespace App\Scheduling\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Scheduling\Api\State\AdminSchedulingSchoolProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/scheduling/schools',
            provider: AdminSchedulingSchoolProvider::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
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
