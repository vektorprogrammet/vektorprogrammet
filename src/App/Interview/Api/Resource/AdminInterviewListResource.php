<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Interview\Api\State\AdminInterviewListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/interviews',
            provider: AdminInterviewListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminInterviewListResource
{
    public array $interviews = [];
}
