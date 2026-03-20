<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

/**
 * Entity having both department and semester.
 */
interface DepartmentSemesterInterface
{
    public function getDepartment();

    public function getSemester();
}
