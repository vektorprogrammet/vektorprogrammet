<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\AdminSurveyNotifierCreateProcessor;
use App\State\AdminSurveyNotifierEditProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'AdminSurveyNotifierWrite',
    operations: [
        new Post(
            uriTemplate: '/admin/survey-notifiers',
            processor: AdminSurveyNotifierCreateProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            status: 201,
        ),
        new Put(
            uriTemplate: '/admin/survey-notifiers/{id}',
            processor: AdminSurveyNotifierEditProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            read: false,
        ),
    ],
)]
class AdminSurveyNotifierWriteResource
{
    public ?int $id = null;

    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    public ?string $name = null;

    public ?int $surveyId = null;

    public ?string $timeOfNotification = null;

    public ?int $notificationType = null;

    public ?string $smsMessage = null;

    public ?string $emailFromName = null;

    public ?string $emailSubject = null;

    public ?string $emailMessage = null;

    public ?string $emailEndMessage = null;

    public ?int $emailType = null;

    /** @var int[]|null */
    public ?array $userGroupIds = null;
}
