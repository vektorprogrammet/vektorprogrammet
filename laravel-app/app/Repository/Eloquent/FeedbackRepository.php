<?php

namespace App\Repository\Eloquent;

use App\Models\Feedback;
use App\Repository\Contract\FeedbackRepositoryInterface;

/**
 * Eloquent implementation of FeedbackRepositoryInterface.
 */
class FeedbackRepository implements FeedbackRepositoryInterface
{
    /**
     * Find all feedback sorted by newest first.
     *
     * @return Feedback[]
     */
    public function findAllSortByNewest(): array
    {
        return Feedback::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}

