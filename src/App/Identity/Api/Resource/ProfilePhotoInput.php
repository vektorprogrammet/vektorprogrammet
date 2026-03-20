<?php

namespace App\Identity\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Identity\Api\State\ProfilePhotoProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/me/photo',
            processor: ProfilePhotoProcessor::class,
            security: "is_granted('ROLE_USER')",
            output: false,
            status: 204,
        ),
    ],
)]
/**
 * Self-only photo upload via /api/me/photo.
 *
 * The original controller (ProfilePhotoController) allowed TEAM_LEADER+ to upload
 * photos for other users. This endpoint intentionally only supports uploading your
 * own photo — admin-on-behalf-of uploads will be a separate endpoint if needed.
 */
class ProfilePhotoInput
{
}
