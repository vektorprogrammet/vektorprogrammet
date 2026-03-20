<?php

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Operations\Api\Resource\AdminReceiptDashboardResource;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Operations\Infrastructure\Repository\ReceiptRepository;
use App\Operations\Domain\Rules\ReceiptStatistics;

class AdminReceiptDashboardProvider implements ProviderInterface
{
    public function __construct(
        private readonly ReceiptRepository $receiptRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminReceiptDashboardResource
    {
        $pendingReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_PENDING);
        $refundedReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_REFUNDED);
        $rejectedReceipts = $this->receiptRepo->findByStatus(Receipt::STATUS_REJECTED);

        $pendingStats = new ReceiptStatistics($pendingReceipts);
        $refundedStats = new ReceiptStatistics($refundedReceipts);

        $resource = new AdminReceiptDashboardResource();
        $resource->pendingCount = count($pendingReceipts);
        $resource->pendingTotalAmount = $pendingStats->totalAmount();
        $resource->refundedCount = count($refundedReceipts);
        $resource->totalPayoutThisYear = $refundedStats->totalPayoutIn(date('Y'));
        $resource->avgRefundTimeHours = $refundedStats->averageRefundTimeInHours();
        $resource->rejectedCount = count($rejectedReceipts);

        return $resource;
    }
}
