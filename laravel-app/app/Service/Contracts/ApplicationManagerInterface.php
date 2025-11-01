<?php

namespace App\Contract;

use App\Models\Application;
use App\Models\ApplicationStatus;

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
