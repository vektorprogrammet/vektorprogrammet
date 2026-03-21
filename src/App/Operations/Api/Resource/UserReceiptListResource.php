<?php

declare(strict_types=1);

namespace App\Operations\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Operations\Api\State\UserReceiptListProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/my/receipts',
            provider: UserReceiptListProvider::class,
            security: "is_granted('ROLE_USER')",
            paginationEnabled: false,
        ),
    ],
)]
class UserReceiptListResource
{
    public ?int $id = null;

    public ?string $visualId = null;

    public ?string $description = null;

    public ?float $sum = null;

    public ?string $receiptDate = null;

    public ?string $submitDate = null;

    public ?string $status = null;

    public ?string $refundDate = null;
}
