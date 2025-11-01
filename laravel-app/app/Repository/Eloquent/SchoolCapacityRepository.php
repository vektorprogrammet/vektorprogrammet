<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\School;
use App\Models\SchoolCapacity;
use App\Models\Semester;
use App\Repository\Contract\SchoolCapacityRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of SchoolCapacityRepositoryInterface.
 */
class SchoolCapacityRepository implements SchoolCapacityRepositoryInterface
{
    /**
     * Find school capacity by school and semester.
     *
     * @param School $school
     * @param Semester $semester
     * @return SchoolCapacity
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySchoolAndSemester($school, $semester): SchoolCapacity
    {
        $schoolCapacity = SchoolCapacity::where('school_id', $school->id)
            ->where('semester_id', $semester->id)
            ->first();

        if (!$schoolCapacity) {
            throw new ModelNotFoundException(
                "SchoolCapacity not found for school {$school->id} and semester {$semester->id}"
            );
        }

        return $schoolCapacity;
    }

    /**
     * Find school capacities by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return SchoolCapacity[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        return SchoolCapacity::where('department_id', $department->id)
            ->where('semester_id', $semester->id)
            ->get()
            ->toArray();
    }
}

