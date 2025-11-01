<?php

namespace App\Contract;

use App\Models\SurveyNotificationCollection;

/**
 * Interface for SurveyNotifier service.
 * Defines contract for survey notification operations.
 */
interface SurveyNotifierInterface
{
    /**
     * Initialize survey notifier.
     *
     * @param SurveyNotificationCollection $surveyNotificationCollection
     */
    public function initializeSurveyNotifier(SurveyNotificationCollection $surveyNotificationCollection);

    /**
     * Send notifications.
     *
     * @param SurveyNotificationCollection $surveyNotificationCollection
     */
    public function sendNotifications(SurveyNotificationCollection $surveyNotificationCollection);
}
