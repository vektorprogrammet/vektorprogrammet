<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\State\AdminExecutiveBoardMemberEditProcessor;
use App\State\AdminExecutiveBoardMemberEditProvider;

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
