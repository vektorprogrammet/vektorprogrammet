<?php

namespace App\Survey\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Survey\Api\State\SurveyPopupProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/surveys/popup',
            provider: SurveyPopupProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
    ],
)]
class SurveyPopupResource
{
    public ?int $id = null;

    public ?string $name = null;
}
