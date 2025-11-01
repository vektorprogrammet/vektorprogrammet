<?php

namespace AppBundle\Service;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\User;
use AppBundle\Service\Contract\FeedbackSubmissionServiceInterface;
use AppBundle\Service\Contract\SlackMessengerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for feedback submission workflow.
 * Extracts business logic from FeedbackController.
 */
class FeedbackSubmissionService implements FeedbackSubmissionServiceInterface
{
    private $entityManager;
    private $slackMessenger;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SlackMessengerInterface $slackMessenger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlackMessengerInterface $slackMessenger
    ) {
        $this->entityManager = $entityManager;
        $this->slackMessenger = $slackMessenger;
    }

    /**
     * {@inheritdoc}
     */
    public function submitFeedback(Feedback $feedback, User $user): void
    {
        $feedback->setUser($user);
        $this->entityManager->persist($feedback);
        $this->entityManager->flush();

        // Notifies on slack (NotificationChannel)
        $this->slackMessenger->notify($feedback->getSlackMessageBody());
    }
}


