<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\State\AdminStaticContentEditProcessor;
use App\State\AdminStaticContentEditProvider;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/static-content/{id}',
            provider: AdminStaticContentEditProvider::class,
            processor: AdminStaticContentEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminStaticContentWriteResource
{
    public ?int $id = null;

    public ?string $html = null;
}
