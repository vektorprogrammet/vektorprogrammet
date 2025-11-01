<?php

namespace App\Repository\Eloquent;

use App\Models\SurveyNotification;
use App\Repository\Contract\SurveyNotificationRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of SurveyNotificationRepositoryInterface.
 */
class SurveyNotificationRepository implements SurveyNotificationRepositoryInterface
{
    /**
     * Find survey notification by user identifier.
     *
     * @param string $identifier
     * @return SurveyNotification|null
     */
    public function findByUserIdentifier(string $identifier): ?SurveyNotification
    {
        return SurveyNotification::where('user_identifier', $identifier)->first();
    }
}

