<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\AdminExecutiveBoardMemberAddProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/executive-board/members',
            processor: AdminExecutiveBoardMemberAddProcessor::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
            status: 201,
        ),
    ],
)]
class AdminExecutiveBoardMemberInput
{
    #[Assert\NotNull]
    public ?int $userId = null;

    #[Assert\NotBlank]
    public string $positionTitle = '';

    #[Assert\NotNull]
    public ?int $startSemesterId = null;

    public ?int $endSemesterId = null;
}
