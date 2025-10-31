<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;

/**
 * Interface for AssistantHistoryData service.
 * Defines contract for assistant history data operations.
 */
interface AssistantHistoryDataInterface
{
    /**
     * Set semester.
     *
     * @param Semester $semester
     * @return AssistantHistoryDataInterface
     */
    public function setSemester(Semester $semester): AssistantHistoryDataInterface;

    /**
     * Set department.
     *
     * @param Department $department
     * @return AssistantHistoryDataInterface
     */
    public function setDepartment(Department $department): AssistantHistoryDataInterface;

    /**
     * Get assistant history count.
     *
     * @return int
     */
    public function getAssistantHistoryCount(): int;

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
     * Get female count.
     *
     * @return int
     */
    public function getFemaleCount(): int;

    /**
     * Get positions count.
     *
     * @return int
     */
    public function getPositionsCount(): int;
}
