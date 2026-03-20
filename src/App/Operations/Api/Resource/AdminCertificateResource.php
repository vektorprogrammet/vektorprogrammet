<?php

namespace App\Operations\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Operations\Api\State\AdminCertificateProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/certificates/{id}',
            provider: AdminCertificateProvider::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminCertificateResource
{
    public ?int $id = null;

    public ?string $userName = null;

    public ?string $schoolName = null;

    public ?string $semesterName = null;

    public ?string $departmentName = null;

    public ?string $workdays = null;

    public ?string $bolk = null;

    public ?string $day = null;
}
