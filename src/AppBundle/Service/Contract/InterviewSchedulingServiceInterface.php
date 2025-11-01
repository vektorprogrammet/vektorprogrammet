<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\User;

/**
 * Interface for InterviewSchedulingService.
 * Defines contract for interview scheduling and assignment operations.
 */
interface InterviewSchedulingServiceInterface
{
    /**
     * Schedule an interview with the provided form data.
     *
     * @param Interview $interview
     * @param array $formData
     * @return void
     */
    public function scheduleInterview(Interview $interview, array $formData): void;

    /**
     * Assign an interviewer to an application with a schema.
     *
     * @param User $interviewer
     * @param Application $application
     * @param InterviewSchema $schema
     * @return void
     */
    public function assignInterviewer(User $interviewer, Application $application, InterviewSchema $schema): void;

    /**
     * Bulk assign interviews to multiple applications.
     *
     * @param User $interviewer
     * @param array $applications
     * @param InterviewSchema $schema
     * @return void
     */
    public function bulkAssignInterviews(User $interviewer, array $applications, InterviewSchema $schema): void;

    /**
     * Validate a map link URL.
     *
     * @param string $link
     * @return bool
     */
    public function validateMapLink(string $link): bool;

    /**
     * Process interview response (accept/cancel/new time request).
     *
     * @param Interview $interview
     * @param string $action One of: 'accept', 'cancel', 'request_new_time'
     * @param array|null $data Additional data for the action
     * @return void
     */
    public function processInterviewResponse(Interview $interview, string $action, ?array $data = null): void;

    /**
     * Check if user can access interview.
     *
     * @param User $user
     * @param Interview $interview
     * @return bool
     */
    public function canUserAccessInterview(User $user, Interview $interview): bool;

    /**
     * Bulk delete interviews for multiple applications.
     *
     * @param array $applications
     * @return void
     */
    public function bulkDeleteInterviews(array $applications): void;

    /**
     * Assign a co-interviewer to an interview.
     *
     * @param Interview $interview
     * @param User $coInterviewer
     * @return void
     */
    public function assignCoInterviewer(Interview $interview, User $coInterviewer): void;

    /**
     * Clear the co-interviewer from an interview.
     *
     * @param Interview $interview
     * @return void
     */
    public function clearCoInterviewer(Interview $interview): void;

    /**
     * Get available co-interviewers for an interview (excluding main interviewer and current co-interviewer).
     *
     * @param Interview $interview
     * @return array List of User entities available as co-interviewers
     */
    public function getAvailableCoInterviewers(Interview $interview): array;

    /**
     * Conduct interview and mark as interviewed.
     *
     * @param Interview $interview
     * @param Application $application
     * @return void
     */
    public function conductInterview(Interview $interview, Application $application): void;

    /**
     * Send schedule email for interview.
     *
     * @param Interview $interview
     * @param array $data
     * @return void
     */
    public function sendScheduleEmail(Interview $interview, array $data): void;
}

