<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Interview\Api\State\InterviewAcceptProcessor;
use App\Interview\Api\State\InterviewCancelProcessor;
use App\Interview\Api\State\InterviewNewTimeProcessor;
use App\Interview\Api\State\InterviewResponseProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/interview-responses/{responseCode}',
            provider: InterviewResponseProvider::class,
        ),
        new Post(
            uriTemplate: '/interview-responses/{responseCode}/accept',
            read: false,
            processor: InterviewAcceptProcessor::class,
            output: false,
            status: 204,
            deserialize: false,
            validate: false,
        ),
        new Post(
            uriTemplate: '/interview-responses/{responseCode}/cancel',
            read: false,
            input: InterviewResponseRejectInput::class,
            processor: InterviewCancelProcessor::class,
            output: false,
            status: 204,
        ),
        new Post(
            uriTemplate: '/interview-responses/{responseCode}/request-new-time',
            read: false,
            input: InterviewResponseNewTimeInput::class,
            processor: InterviewNewTimeProcessor::class,
            output: false,
            status: 204,
        ),
    ],
)]
class InterviewResponseResource
{
    #[ApiProperty(identifier: false)]
    public ?int $id = null;

    #[ApiProperty(identifier: true)]
    public ?string $responseCode = null;

    public ?string $scheduled = null;
    public ?string $room = null;
    public ?string $campus = null;
    public ?string $mapLink = null;
    public ?string $interviewerName = null;
    public ?string $status = null;
    public ?string $cancelMessage = null;
    public ?string $newTimeMessage = null;
}
