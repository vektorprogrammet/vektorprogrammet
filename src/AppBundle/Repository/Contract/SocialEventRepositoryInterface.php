<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\SocialEvent;

/**
 * Interface for SocialEvent repository operations.
 * This interface defines the contract for social event data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SocialEventRepositoryInterface
{
    /**
     * Find social events by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array;

    /**
     * Find future social events by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findFutureSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array;
}

