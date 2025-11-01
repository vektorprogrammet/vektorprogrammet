<?php

namespace App\Contract;

use App\Models\Department;
use App\Models\Semester;

/**
 * Interface for InterviewNotificationManager service.
 * Defines contract for interview notification operations.
 */
interface InterviewNotificationManagerInterface
{
    /**
     * Send application count notification.
     *
     * @param Department $department
     * @param Semester $semester
     */
    public function sendApplicationCountNotification(Department $department, Semester $semester);

    /**
     * Send interviews completed notification.
     *
     * @param Department $department
     * @param Semester $semester
     */
    public function sendInterviewsCompletedNotification(Department $department, Semester $semester);
}
