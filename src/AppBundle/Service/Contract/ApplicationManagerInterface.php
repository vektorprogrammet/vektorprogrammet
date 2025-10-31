<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Application;
use AppBundle\Model\ApplicationStatus;

/**
 * Interface for ApplicationManager service.
 * Defines contract for application management operations.
 */
interface ApplicationManagerInterface
{
    /**
     * Get application status for the given application.
     *
     * @param Application $application
     * @return ApplicationStatus
     */
    public function getApplicationStatus(Application $application): ApplicationStatus;
}
