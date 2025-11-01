<?php

namespace App\Repository\Contract;

use App\Models\SurveyNotification;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for SurveyNotification repository operations.
 * This interface defines the contract for survey notification data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SurveyNotificationRepositoryInterface
{
    /**
     * Find survey notification by user identifier.
     *
     * @param string $identifier
     * @return SurveyNotification|null
     * @throws NonUniqueResultException
     */
    public function findByUserIdentifier(string $identifier): ?SurveyNotification;
}

