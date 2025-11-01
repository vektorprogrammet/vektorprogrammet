<?php

namespace App\Contract;

use App\Models\Feedback;
use App\Models\User;

/**
 * Interface for FeedbackSubmission service operations.
 * Defines contract for feedback submission workflow.
 */
interface FeedbackSubmissionServiceInterface
{
    /**
     * Submit feedback workflow.
     * Persists the feedback, associates it with the user, and sends Slack notification.
     *
     * @param Feedback $feedback
     * @param User $user
     * @return void
     */
    public function submitFeedback(Feedback $feedback, User $user): void;
}


