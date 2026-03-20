<?php

namespace App\Content\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Content\Api\State\PartnersProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me/partners',
            provider: PartnersProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class PartnersResource
{
    public array $partners = [];
}
