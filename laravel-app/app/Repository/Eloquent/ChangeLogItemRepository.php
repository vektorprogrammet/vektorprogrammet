<?php

namespace App\Repository\Eloquent;

use App\Models\ChangeLogItem;
use App\Repository\Contract\ChangeLogItemRepositoryInterface;

/**
 * Eloquent implementation of ChangeLogItemRepositoryInterface.
 */
class ChangeLogItemRepository implements ChangeLogItemRepositoryInterface
{
    /**
     * Find all change log items ordered by date.
     *
     * @return ChangeLogItem[]
     */
    public function findAllOrderedByDate(): array
    {
        return ChangeLogItem::orderBy('date')
            ->get()
            ->toArray();
    }
}

