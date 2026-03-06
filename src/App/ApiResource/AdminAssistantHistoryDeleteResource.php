<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminAssistantHistoryDeleteProcessor;
use App\State\AdminAssistantHistoryDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/assistant-histories/{id}',
            provider: AdminAssistantHistoryDeleteProvider::class,
            processor: AdminAssistantHistoryDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminAssistantHistoryDeleteResource
{
    public ?int $id = null;
}
