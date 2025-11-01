<?php

namespace App\Service;

use App\Models\Feedback;
use App\Models\User;
use App\Service\Contract\FeedbackSubmissionServiceInterface;
use App\Service\Contract\SlackMessengerInterface;

/**
 * Service for feedback submission workflow.
 * Extracts business logic from FeedbackController.
 */
class FeedbackSubmissionService implements FeedbackSubmissionServiceInterface
{
    private SlackMessengerInterface $slackMessenger;

    /**
     * @param SlackMessengerInterface $slackMessenger
     */
    public function __construct(
        SlackMessengerInterface $slackMessenger
    ) {
        $this->slackMessenger = $slackMessenger;
    }

    /**
     * {@inheritdoc}
     */
    public function submitFeedback(Feedback $feedback, User $user): void
    {
        $feedback->user_id = $user->id;
        $feedback->save();

        // Notifies on slack (NotificationChannel)
        $this->slackMessenger->notify($feedback->getSlackMessageBody());
    }
}


