<?php

namespace AppBundle\Service\Contract;

/**
 * Interface for SbsData service.
 * Extends ApplicationDataInterface.
 */
interface SbsDataInterface extends ApplicationDataInterface
{
    /**
     * Get total applications count.
     *
     * @return int
     */
    public function getTotalApplicationsCount(): int;

    /**
     * Get step.
     *
     * @return int
     */
    public function getStep(): int;

    /**
     * Get step progress.
     *
     * @return float
     */
    public function getStepProgress(): float;

    /**
     * Get admission time left.
     *
     * @return int
     */
    public function getAdmissionTimeLeft(): int;

    /**
     * Get time to admission start.
     *
     * @return int
     */
    public function getTimeToAdmissionStart(): int;
}
