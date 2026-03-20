<?php

namespace App\Shared\Contracts;

use App\Organization\Infrastructure\Entity\Department;
use App\Shared\Entity\Semester;

/**
 * Entity having both department and semester.
 */
interface DepartmentSemesterInterface
{
    /**
     * @return Department
     */
    public function getDepartment();

    /**
     * @return Semester
     */
    public function getSemester();
}
