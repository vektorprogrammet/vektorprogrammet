<?php

namespace App\Contract;

use App\Models\Survey;
use App\Models\SurveyTaken;
use App\Models\User;

/**
 * Interface for SurveyManager service.
 * Defines contract for survey management operations.
 */
interface SurveyManagerInterface
{
    /**
     * Initialize a survey taken for the given survey.
     *
     * @param Survey $survey
     * @return SurveyTaken
     */
    public function initializeSurveyTaken(Survey $survey): SurveyTaken;

    /**
     * Initialize user survey taken.
     *
     * @param Survey $survey
     * @param User $user
     * @return SurveyTaken
     */
    public function initializeUserSurveyTaken(Survey $survey, User $user): SurveyTaken;

    /**
     * Predict survey taken answers based on previous answers.
     *
     * @param SurveyTaken $surveyTaken
     * @return SurveyTaken
     */
    public function predictSurveyTakenAnswers(SurveyTaken $surveyTaken): SurveyTaken;

    /**
     * Get user affiliation of survey answers.
     *
     * @param Survey $survey
     * @return array
     */
    public function getUserAffiliationOfSurveyAnswers(Survey $survey): array;

    /**
     * Get text answer with school results.
     *
     * @param Survey $survey
     * @return array
     */
    public function getTextAnswerWithSchoolResults(Survey $survey): array;

    /**
     * Get text answer with team results.
     *
     * @param Survey $survey
     * @return array
     */
    public function getTextAnswerWithTeamResults(Survey $survey): array;

    /**
     * Get team names for survey taker.
     *
     * @param SurveyTaken $taken
     * @return string
     */
    public function getTeamNamesForSurveyTaker(SurveyTaken $taken): string;

    /**
     * Convert survey result to JSON.
     *
     * @param Survey $survey
     * @return array
     */
    public function surveyResultToJson(Survey $survey): array;

    /**
     * Toggle reserved from popup for user.
     *
     * @param User $user
     */
    public function toggleReservedFromPopUp(User $user);

    /**
     * Get survey target audience string.
     *
     * @param Survey $survey
     * @return string
     */
    public function getSurveyTargetAudienceString(Survey $survey): string;

    /**
     * Convert survey results to CSV.
     *
     * @param Survey $survey
     * @return string
     */
    public function surveyResultsToCsv(Survey $survey): string;
}
