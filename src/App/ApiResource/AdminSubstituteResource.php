<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\AdminSubstituteActivateProcessor;
use App\State\AdminSubstituteActivateProvider;
use App\State\AdminSubstituteDeactivateProcessor;
use App\State\AdminSubstituteDeactivateProvider;
use App\State\AdminSubstituteEditProcessor;
use App\State\AdminSubstituteEditProvider;
use App\State\AdminSubstituteListProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/substitutes',
            provider: AdminSubstituteListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
        new Put(
            uriTemplate: '/admin/substitutes/{id}',
            provider: AdminSubstituteEditProvider::class,
            processor: AdminSubstituteEditProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
        new Post(
            uriTemplate: '/admin/substitutes/{id}/activate',
            deserialize: false,
            provider: AdminSubstituteActivateProvider::class,
            processor: AdminSubstituteActivateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
        new Post(
            uriTemplate: '/admin/substitutes/{id}/deactivate',
            deserialize: false,
            provider: AdminSubstituteDeactivateProvider::class,
            processor: AdminSubstituteDeactivateProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminSubstituteResource
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $email = null;

    public ?int $yearOfStudy = null;

    public ?string $language = null;

    public ?bool $monday = null;

    public ?bool $tuesday = null;

    public ?bool $wednesday = null;

    public ?bool $thursday = null;

    public ?bool $friday = null;
}
