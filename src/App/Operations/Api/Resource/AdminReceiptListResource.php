<?php

declare(strict_types=1);

namespace App\Operations\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Operations\Api\State\AdminReceiptListProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/receipts',
            provider: AdminReceiptListProvider::class,
            security: "is_granted('ROLE_TEAM_MEMBER')",
            paginationItemsPerPage: 30,
        ),
    ],
)]
class AdminReceiptListResource
{
    public ?int $id = null;
    public ?string $visualId = null;
    public ?string $userName = null;
    public ?string $description = null;
    public ?float $sum = null;
    public ?string $receiptDate = null;
    public ?string $submitDate = null;
    public ?string $status = null;
}
