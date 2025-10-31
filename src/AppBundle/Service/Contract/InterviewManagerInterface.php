<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\User;

/**
 * Interface for InterviewManager service.
 * Defines contract for interview management operations.
 */
interface InterviewManagerInterface
{
    /**
     * Check if the logged-in user can see the interview.
     *
     * @param Interview $interview
     * @return bool
     */
    public function loggedInUserCanSeeInterview(Interview $interview): bool;

    /**
     * Initialize interview answers for the given interview.
     *
     * @param Interview $interview
     * @return Interview
     */
    public function initializeInterviewAnswers(Interview $interview): Interview;

    /**
     * Assign an interviewer to an application.
     *
     * @param User $interviewer
     * @param Application $application
     */
    public function assignInterviewerToApplication(User $interviewer, Application $application);

    /**
     * Send schedule email for interview.
     *
     * @param Interview $interview
     * @param array $data
     */
    public function sendScheduleEmail(Interview $interview, array $data);

    /**
     * Send reschedule email for interview.
     *
     * @param Interview $interview
     */
    public function sendRescheduleEmail(Interview $interview);

    /**
     * Send cancel email for interview.
     *
     * @param Interview $interview
     */
    public function sendCancelEmail(Interview $interview);

    /**
     * Send interview schedule to interviewer.
     *
     * @param User $interviewer
     */
    public function sendInterviewScheduleToInterviewer(User $interviewer);

    /**
     * Send accept interview reminders.
     */
    public function sendAcceptInterviewReminders();

    /**
     * Get default schedule form data for interview.
     *
     * @param Interview $interview
     * @return array
     */
    public function getDefaultScheduleFormData(Interview $interview): array;
}
