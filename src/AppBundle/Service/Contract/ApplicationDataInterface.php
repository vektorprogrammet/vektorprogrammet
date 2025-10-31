<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;

/**
 * Interface for ApplicationData service.
 * Defines contract for application data operations.
 */
interface ApplicationDataInterface
{
    /**
     * Set department.
     *
     * @param Department $department
     */
    public function setDepartment(Department $department);

    /**
     * Set admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     */
    public function setAdmissionPeriod(AdmissionPeriod $admissionPeriod);

    /**
     * Get application count.
     *
     * @return int
     */
    public function getApplicationCount(): int;

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount(): int;

    /**
     * Get male count.
     *
     * @return int
     */
    public function getMaleCount(): int;

    /**
     * Get male percentage.
     *
     * @return float
     */
    public function getMalePercentage(): float;

    /**
     * Get female count.
     *
     * @return int
     */
    public function getFemaleCount(): int;

    /**
     * Get female percentage.
     *
     * @return float
     */
    public function getFemalePercentage(): float;

    /**
     * Get previous participation count.
     *
     * @return int
     */
    public function getPreviousParticipationCount(): int;

    /**
     * Get cancelled interviews count.
     *
     * @return int
     */
    public function getCancelledInterviewsCount(): int;

    /**
     * Get interviewed assistants count.
     *
     * @return int
     */
    public function getInterviewedAssistantsCount(): int;

    /**
     * Get assigned interviews count.
     *
     * @return int
     */
    public function getAssignedInterviewsCount(): int;

    /**
     * Get total assistants count.
     *
     * @return int
     */
    public function getTotalAssistantsCount(): int;

    /**
     * Get positions count.
     *
     * @return int
     */
    public function getPositionsCount(): int;

    /**
     * Get total interviews count.
     *
     * @return int
     */
    public function getTotalInterviewsCount(): int;

    /**
     * Get applicants not yet interviewed count.
     *
     * @return int
     */
    public function applicantsNotYetInterviewedCount();

    /**
     * Get interviews left count.
     *
     * @return int
     */
    public function getInterviewsLeftCount(): int;

    /**
     * Get fields of study counts.
     *
     * @return array
     */
    public function getFieldsOfStudyCounts(): array;

    /**
     * Get study year counts.
     *
     * @return array
     */
    public function getStudyYearCounts(): array;

    /**
     * Get admission period.
     *
     * @return AdmissionPeriod
     */
    public function getAdmissionPeriod(): AdmissionPeriod;

    /**
     * Get department.
     *
     * @return Department
     */
    public function getDepartment(): Department;

    /**
     * Get heard about from.
     *
     * @return array
     */
    public function getHeardAboutFrom(): array;
}
