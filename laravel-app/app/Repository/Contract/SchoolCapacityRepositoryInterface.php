<?php

namespace App\Repository\Contract;

use App\Models\Department;
use App\Models\School;
use App\Models\SchoolCapacity;
use App\Models\Semester;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for SchoolCapacity repository operations.
 * This interface defines the contract for school capacity data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SchoolCapacityRepositoryInterface
{
    /**
     * Find school capacity by school and semester.
     *
     * @param School $school
     * @param Semester $semester
     * @return SchoolCapacity
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findBySchoolAndSemester($school, $semester): SchoolCapacity;

    /**
     * Find school capacities by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return SchoolCapacity[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array;
}

