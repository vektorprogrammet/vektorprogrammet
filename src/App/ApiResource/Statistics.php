<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\StatisticsProvider;

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
