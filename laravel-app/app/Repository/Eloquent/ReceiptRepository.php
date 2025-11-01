<?php

namespace App\Repository\Eloquent;

use App\Models\Receipt;
use App\Models\User;
use App\Repository\Contract\ReceiptRepositoryInterface;

/**
 * Eloquent implementation of ReceiptRepositoryInterface.
 */
class ReceiptRepository implements ReceiptRepositoryInterface
{
    /**
     * Find receipts by user.
     *
     * @param User $user
     * @return Receipt[]
     */
    public function findByUser(User $user): array
    {
        return Receipt::where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Find receipts by status.
     *
     * @param string $status
     * @return Receipt[]
     */
    public function findByStatus(string $status): array
    {
        return Receipt::where('status', $status)
            ->get()
            ->toArray();
    }
}

