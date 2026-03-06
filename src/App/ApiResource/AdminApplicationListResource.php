<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AdminApplicationListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/applications',
            provider: AdminApplicationListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminApplicationListResource
{
    public string $status = 'new';

    public array $applications = [];
}
