<?php

namespace App\Support\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Support\Api\State\DashboardProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me/dashboard',
            provider: DashboardProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class DashboardResource
{
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public ?array $activeApplication = null;

    public array $activeAssistantHistories = [];
}
