<?php

namespace App\Admission\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Admission\Api\State\AdminApplicationBulkDeleteProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/applications/bulk-delete',
            processor: AdminApplicationBulkDeleteProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            output: false,
            status: 204,
        ),
    ],
)]
class AdminApplicationBulkDeleteInput
{
    /** @var int[] */
    #[Assert\Count(max: 100)]
    #[Assert\All([new Assert\Type('int')])]
    public array $ids = [];
}
