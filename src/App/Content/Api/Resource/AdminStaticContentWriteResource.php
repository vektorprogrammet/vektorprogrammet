<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Content\Api\State\AdminStaticContentEditProcessor;
use App\Content\Api\State\AdminStaticContentEditProvider;

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
