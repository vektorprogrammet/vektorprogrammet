<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\PopupDismissProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/me/popup-dismiss',
            processor: PopupDismissProcessor::class,
            security: "is_granted('ROLE_USER')",
            output: false,
            status: 204,
        ),
    ],
)]
class PopupDismissInput
{
}
