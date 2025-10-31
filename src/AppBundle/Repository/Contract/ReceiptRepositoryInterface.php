<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;

/**
 * Interface for Receipt repository operations.
 * This interface defines the contract for receipt data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ReceiptRepositoryInterface
{
    /**
     * Find receipts by user.
     *
     * @param User $user
     * @return Receipt[]
     */
    public function findByUser(User $user): array;

    /**
     * Find receipts by status.
     *
     * @param string $status
     * @return Receipt[]
     */
    public function findByStatus(string $status): array;
}

