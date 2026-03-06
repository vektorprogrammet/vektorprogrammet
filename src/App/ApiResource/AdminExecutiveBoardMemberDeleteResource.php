<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminExecutiveBoardMemberDeleteProcessor;
use App\State\AdminExecutiveBoardMemberDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/executive-board/members/{id}',
            provider: AdminExecutiveBoardMemberDeleteProvider::class,
            processor: AdminExecutiveBoardMemberDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminExecutiveBoardMemberDeleteResource
{
    public ?int $id = null;
}
