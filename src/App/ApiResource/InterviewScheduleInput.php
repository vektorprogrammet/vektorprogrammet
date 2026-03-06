<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\InterviewScheduleProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/interviews/{id}/schedule',
            read: false,
            processor: InterviewScheduleProcessor::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewScheduleInput
{
    public string $datetime = '';
    public string $room = '';
    public string $campus = '';
    public string $mapLink = '';
    public string $from = '';
    public string $to = '';
    public string $message = '';
}
