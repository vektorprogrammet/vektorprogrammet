<?php

namespace App\Interview\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Interview\Api\State\AdminInterviewListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/interviews',
            provider: AdminInterviewListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class AdminInterviewListResource
{
    /**
     * @var list<array{
     *   id: int,
     *   applicantName: string,
     *   interviewerName: string|null,
     *   scheduled: string|null,
     *   status: string,
     *   interviewed: bool,
     *   coInterviewer: string|null,
     *   room: string|null,
     *   campus: string|null,
     *   mapLink: string|null
     * }>
     */
    public array $interviews = [];
}
