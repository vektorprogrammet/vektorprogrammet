<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Semester;

/**
 * Interface for semester validation logic.
 */
interface SemesterValidationServiceInterface
{
    /**
     * Check if a semester with the given time and year already exists.
     *
     * @param int $semesterTime The semester time (e.g., 1 for Spring, 2 for Fall)
     * @param int $year The year
     * @return Semester|null The existing semester if found, null otherwise
     */
    public function findExistingSemester(int $semesterTime, int $year): ?Semester;
}

