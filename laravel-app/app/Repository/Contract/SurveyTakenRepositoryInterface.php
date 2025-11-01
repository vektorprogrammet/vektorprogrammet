<?php

namespace App\Repository\Contract;

use App\Models\Survey;
use App\Models\SurveyTaken;
use App\Models\User;

/**
 * Interface for SurveyTaken repository operations.
 * This interface defines the contract for survey taken data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SurveyTakenRepositoryInterface
{
    /**
     * Find all survey taken records by survey.
     *
     * @param Survey $survey
     * @return SurveyTaken[]
     */
    public function findAllTakenBySurvey(Survey $survey): array;

    /**
     * Find all survey taken records by survey and user.
     *
     * @param Survey $survey
     * @param User $user
     * @return SurveyTaken[]
     */
    public function findAllBySurveyAndUser(Survey $survey, User $user): array;
}

