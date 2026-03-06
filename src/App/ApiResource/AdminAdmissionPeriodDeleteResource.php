<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\AdminAdmissionPeriodDeleteProcessor;
use App\State\AdminAdmissionPeriodDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/admission-periods/{id}',
            provider: AdminAdmissionPeriodDeleteProvider::class,
            processor: AdminAdmissionPeriodDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminAdmissionPeriodDeleteResource
{
    public ?int $id = null;
}
