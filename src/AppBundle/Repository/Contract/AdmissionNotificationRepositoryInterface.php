<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\AdmissionNotification;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;

/**
 * Interface for AdmissionNotification repository operations.
 * This interface defines the contract for admission notification data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AdmissionNotificationRepositoryInterface
{
    /**
     * Find emails by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return string[]
     */
    public function findEmailsBySemesterAndDepartment(Semester $semester, Department $department): array;

    /**
     * Find emails by semester and department for info meeting.
     *
     * @param Semester $semester
     * @param Department $department
     * @return string[]
     */
    public function findEmailsBySemesterAndDepartmentAndInfoMeeting(Semester $semester, Department $department): array;
}

