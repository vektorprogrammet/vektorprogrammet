<?php

namespace App\Repository\Contract;

use App\Models\Feedback;

/**
 * Interface for Feedback repository operations.
 * This interface defines the contract for feedback data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface FeedbackRepositoryInterface
{
    /**
     * Find all feedback sorted by newest first.
     *
     * @return Feedback[]
     */
    public function findAllSortByNewest(): array;
}

