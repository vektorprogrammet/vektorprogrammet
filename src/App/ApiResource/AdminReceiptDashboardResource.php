<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AdminReceiptDashboardProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/receipts',
            provider: AdminReceiptDashboardProvider::class,
            security: "is_granted('ROLE_TEAM_LEADER')",
        ),
    ],
)]
class AdminReceiptDashboardResource
{
    public int $pendingCount = 0;

    public float $pendingTotalAmount = 0.0;

    public int $refundedCount = 0;

    public float $totalPayoutThisYear = 0.0;

    public int $avgRefundTimeHours = 0;

    public int $rejectedCount = 0;
}
