<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Admission\Api\State\AdmissionStatisticsProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/admission-statistics',
            provider: AdmissionStatisticsProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdmissionStatisticsResource
{
    public int $applicationCount = 0;

    public int $maleApplications = 0;

    public int $femaleApplications = 0;

    public int $assistantCount = 0;

    public int $maleAssistants = 0;

    public int $femaleAssistants = 0;

    public string $departmentName = '';

    public string $semesterName = '';
}
