<?php

namespace App\Organization\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Organization\Api\State\MailingListProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/mailing-lists',
            provider: MailingListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
        ),
    ],
)]
class MailingListResource
{
    public string $type = 'assistants';

    public array $users = [];

    public int $count = 0;
}
