<?php

namespace App\Support\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Support\Api\State\StatisticsProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/statistics',
            provider: StatisticsProvider::class,
        ),
    ],
)]
class Statistics
{
    public int $assistantCount;
    public int $teamMemberCount;
    public int $femaleAssistantCount;
    public int $maleAssistantCount;
}
