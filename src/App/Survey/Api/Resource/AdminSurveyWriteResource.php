<?php

namespace App\Survey\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Survey\Api\State\AdminSurveyCreateProcessor;
use App\Survey\Api\State\AdminSurveyEditProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'AdminSurveyWrite',
    operations: [
        new Post(
            uriTemplate: '/admin/surveys',
            processor: AdminSurveyCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/surveys/{id}',
            processor: AdminSurveyEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            read: false,
        ),
    ],
)]
class AdminSurveyWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    public ?string $name = null;

    public ?int $semesterId = null;

    public ?int $departmentId = null;

    public ?int $targetAudience = null;

    public ?bool $confidential = null;

    public ?string $finishPageContent = null;

    public ?bool $showCustomPopUpMessage = null;

    public ?string $surveyPopUpMessage = null;

    /** @var array<array{question: string, type: string, optional?: bool, help?: string, alternatives?: string[]}>|null */
    public ?array $questions = null;
}
