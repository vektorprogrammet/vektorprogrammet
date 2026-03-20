<?php

declare(strict_types=1);

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Organization\Api\State\AdminExecutiveBoardMemberDeleteProcessor;
use App\Organization\Api\State\AdminExecutiveBoardMemberDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/executive-board/members/{id}',
            provider: AdminExecutiveBoardMemberDeleteProvider::class,
            processor: AdminExecutiveBoardMemberDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminExecutiveBoardMemberDeleteResource
{
    public ?int $id = null;
}
