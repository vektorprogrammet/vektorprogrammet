<?php

namespace App\Entity;

/**
 * Entity having both department and semester
 *
 * @package App\Entity
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
