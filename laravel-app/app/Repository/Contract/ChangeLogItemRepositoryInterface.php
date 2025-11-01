<?php

namespace App\Repository\Contract;

use App\Models\ChangeLogItem;

/**
 * Interface for ChangeLogItem repository operations.
 * This interface defines the contract for change log item data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ChangeLogItemRepositoryInterface
{
    /**
     * Find all change log items ordered by date.
     *
     * @return ChangeLogItem[]
     */
    public function findAllOrderedByDate(): array;
}

