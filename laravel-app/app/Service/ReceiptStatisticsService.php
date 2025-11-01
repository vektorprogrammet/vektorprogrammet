<?php

namespace App\Service;

use App\Models\Receipt;
use App\Models\User;
use App\Event\ReceiptEvent;
use App\Repository\Contract\ReceiptRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Service\Contract\ReceiptStatisticsServiceInterface;
use App\Service\Contract\SorterInterface;
use App\Utils\ReceiptStatistics;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service for receipt statistics calculation and status management.
 * Extracts business logic from ReceiptController.
 */
class ReceiptStatisticsService implements ReceiptStatisticsServiceInterface
{
    private $receiptRepository;
    private $userRepository;
    private $sorter;
    private $eventDispatcher;

    /**
     * @param ReceiptRepositoryInterface $receiptRepository
     * @param UserRepositoryInterface $userRepository
     * @param SorterInterface $sorter
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ReceiptRepositoryInterface $receiptRepository,
        UserRepositoryInterface $userRepository,
        SorterInterface $sorter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->receiptRepository = $receiptRepository;
        $this->userRepository = $userRepository;
        $this->sorter = $sorter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateReceiptStatistics(): array
    {
        $usersWithReceipts = $this->userRepository->findAllUsersWithReceipts();
        $refundedReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_REFUNDED);
        $pendingReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_PENDING);
        $rejectedReceipts = $this->receiptRepository->findByStatus(Receipt::STATUS_REJECTED);

        $refundedReceiptStatistics = new ReceiptStatistics($refundedReceipts);
        $totalPayoutThisYear = $refundedReceiptStatistics->totalPayoutIn((new DateTime())->format('Y'));
        $avgRefundTimeInHours = $refundedReceiptStatistics->averageRefundTimeInHours();

        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);
        $rejectedReceiptStatistics = new ReceiptStatistics($rejectedReceipts);

        $this->sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $this->sorter->sortUsersByReceiptStatus($usersWithReceipts);

        return [
            'users_with_receipts' => $usersWithReceipts,
            'total_payout' => $totalPayoutThisYear,
            'avg_refund_time_in_hours' => $avgRefundTimeInHours,
            'pending_statistics' => $pendingReceiptStatistics,
            'rejected_statistics' => $rejectedReceiptStatistics,
            'refunded_statistics' => $refundedReceiptStatistics,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSortedReceiptsForUser(User $user): array
    {
        $receipts = $this->receiptRepository->findByUser($user);

        $this->sorter->sortReceiptsBySubmitTime($receipts);
        $this->sorter->sortReceiptsByStatus($receipts);

        return $receipts;
    }

    /**
     * {@inheritdoc}
     */
    public function processStatusChange(Receipt $receipt, string $status): void
    {
        if ($status !== Receipt::STATUS_PENDING &&
            $status !== Receipt::STATUS_REFUNDED &&
            $status !== Receipt::STATUS_REJECTED) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $receipt->setStatus($status);

        if ($status === Receipt::STATUS_REFUNDED && !$receipt->getRefundDate()) {
            $receipt->setRefundDate(new DateTime());
        }

        // Dispatch appropriate event
        if ($status === Receipt::STATUS_REFUNDED) {
            $this->eventDispatcher->dispatch(ReceiptEvent::REFUNDED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->eventDispatcher->dispatch(ReceiptEvent::REJECTED, new ReceiptEvent($receipt));
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->eventDispatcher->dispatch(ReceiptEvent::PENDING, new ReceiptEvent($receipt));
        }
    }
}


