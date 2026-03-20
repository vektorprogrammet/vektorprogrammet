<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Admission\Api\State\AdminAdmissionPeriodDeleteProcessor;
use App\Admission\Api\State\AdminAdmissionPeriodDeleteProvider;

#[ApiResource(
    operations: [
        new Delete(
            uriTemplate: '/admin/admission-periods/{id}',
            provider: AdminAdmissionPeriodDeleteProvider::class,
            processor: AdminAdmissionPeriodDeleteProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminAdmissionPeriodDeleteResource
{
    public ?int $id = null;
}
