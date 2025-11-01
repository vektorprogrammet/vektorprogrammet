<?php

namespace App\Repository\Eloquent;

use App\Models\Survey;
use App\Models\SurveyTaken;
use App\Models\User;
use App\Repository\Contract\SurveyTakenRepositoryInterface;

/**
 * Eloquent implementation of SurveyTakenRepositoryInterface.
 */
class SurveyTakenRepository implements SurveyTakenRepositoryInterface
{
    /**
     * Find all survey taken records by survey.
     *
     * @param Survey $survey
     * @return SurveyTaken[]
     */
    public function findAllTakenBySurvey(Survey $survey): array
    {
        return SurveyTaken::where('survey_id', $survey->id)
            ->get()
            ->toArray();
    }

    /**
     * Find all survey taken records by survey and user.
     *
     * @param Survey $survey
     * @param User $user
     * @return SurveyTaken[]
     */
    public function findAllBySurveyAndUser(Survey $survey, User $user): array
    {
        return SurveyTaken::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->get()
            ->toArray();
    }
}

