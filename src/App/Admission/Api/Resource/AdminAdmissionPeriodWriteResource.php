<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Admission\Api\State\AdminAdmissionPeriodCreateProcessor;
use App\Admission\Api\State\AdminAdmissionPeriodEditProcessor;
use App\Admission\Api\State\AdminAdmissionPeriodEditProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/admission-periods',
            processor: AdminAdmissionPeriodCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/admission-periods/{id}',
            provider: AdminAdmissionPeriodEditProvider::class,
            processor: AdminAdmissionPeriodEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminAdmissionPeriodWriteResource
{
    public ?int $id = null;

    #[Assert\NotNull]
    public ?int $departmentId = null;

    #[Assert\NotNull]
    public ?int $semesterId = null;

    #[Assert\NotBlank]
    public ?string $startDate = null;

    #[Assert\NotBlank]
    public ?string $endDate = null;
}
