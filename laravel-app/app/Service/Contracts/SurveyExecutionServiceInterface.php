<?php

namespace App\Contract;

use App\Models\Survey;
use App\Models\SurveyTaken;
use App\Models\User;

/**
 * Interface for SurveyExecutionService.
 * Defines contract for survey execution and processing operations.
 */
interface SurveyExecutionServiceInterface
{
    /**
     * Execute survey for a user.
     *
     * @param Survey $survey
     * @param User|null $user
     * @param string|null $identifier
     * @return array Contains 'surveyTaken', 'formData', and other execution context
     */
    public function executeSurvey(Survey $survey, User $user = null, string $identifier = null): array;

    /**
     * Process survey submission.
     *
     * @param Survey $survey
     * @param SurveyTaken $surveyTaken
     * @param User|null $user
     * @return void
     */
    public function processSubmission(Survey $survey, SurveyTaken $surveyTaken, User $user = null): void;

    /**
     * Calculate survey statistics.
     *
     * @param Survey $survey
     * @return array Statistics data
     */
    public function calculateStatistics(Survey $survey): array;

    /**
     * Check if user has access to survey.
     *
     * @param User $user
     * @param Survey $survey
     * @return bool
     */
    public function checkAccess(User $user, Survey $survey): bool;

    /**
     * Prepare survey results for display.
     *
     * @param Survey $survey
     * @return array Results data
     */
    public function prepareResults(Survey $survey): array;
}

