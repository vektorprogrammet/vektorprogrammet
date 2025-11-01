<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use AppBundle\Utils\ReceiptStatistics;

/**
 * Interface for ReceiptStatistics service operations.
 * Defines contract for receipt statistics calculation and status management.
 */
interface ReceiptStatisticsServiceInterface
{
    /**
     * Calculate all receipt statistics including payout and refund time.
     *
     * @return array Statistics data with keys:
     *   - users_with_receipts: User[]
     *   - total_payout: float
     *   - avg_refund_time_in_hours: int
     *   - pending_statistics: ReceiptStatistics
     *   - rejected_statistics: ReceiptStatistics
     *   - refunded_statistics: ReceiptStatistics
     */
    public function calculateReceiptStatistics(): array;

    /**
     * Get sorted receipts for a specific user.
     *
     * @param User $user
     * @return Receipt[]
     */
    public function getSortedReceiptsForUser(User $user): array;

    /**
     * Process receipt status change workflow.
     * Handles status validation, refund date management, and event dispatching.
     *
     * @param Receipt $receipt
     * @param string $status
     * @return void
     * @throws \InvalidArgumentException If status is invalid
     */
    public function processStatusChange(Receipt $receipt, string $status): void;
}


