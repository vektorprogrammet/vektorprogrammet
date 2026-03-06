<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\InterviewSchemaListProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/interview-schemas',
            provider: InterviewSchemaListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class InterviewSchemaResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    public ?string $name = null;

    public int $questionCount = 0;
}
