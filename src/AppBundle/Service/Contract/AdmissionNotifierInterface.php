<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;

/**
 * Interface for AdmissionNotifier service.
 * Defines contract for admission notification operations.
 */
interface AdmissionNotifierInterface
{
    /**
     * Create subscription for department.
     *
     * @param Department $department
     * @param string $email
     * @param bool $infoMeeting
     * @param bool $fromApplication
     */
    public function createSubscription(Department $department, string $email, bool $infoMeeting = false, bool $fromApplication = false);

    /**
     * Send admission notifications.
     */
    public function sendAdmissionNotifications();

    /**
     * Send info meeting notifications.
     */
    public function sendInfoMeetingNotifications();
}
