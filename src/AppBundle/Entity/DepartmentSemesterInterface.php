<?php

namespace AppBundle\Entity;

/**
 * Entity having both department and semester
 *
 * @package AppBundle\Entity
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
