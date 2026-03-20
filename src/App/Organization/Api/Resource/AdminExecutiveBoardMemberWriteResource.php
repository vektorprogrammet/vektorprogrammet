<?php

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Organization\Api\State\AdminExecutiveBoardMemberEditProcessor;
use App\Organization\Api\State\AdminExecutiveBoardMemberEditProvider;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/admin/executive-board/members/{id}',
            provider: AdminExecutiveBoardMemberEditProvider::class,
            processor: AdminExecutiveBoardMemberEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminExecutiveBoardMemberWriteResource
{
    public ?int $id = null;

    public ?string $positionTitle = null;

    public ?int $startSemesterId = null;

    public ?int $endSemesterId = null;
}
