<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\FieldOfStudy;
use App\Repository\Contract\FieldOfStudyRepositoryInterface;

/**
 * Eloquent implementation of FieldOfStudyRepositoryInterface.
 */
class FieldOfStudyRepository implements FieldOfStudyRepositoryInterface
{
    /**
     * Find all fields of study.
     *
     * @return FieldOfStudy[]
     */
    public function findAllFieldOfStudy(): array
    {
        return FieldOfStudy::query()
            ->distinct()
            ->get()
            ->toArray();
    }

    /**
     * Find fields of study by department.
     *
     * @param Department $department
     * @return FieldOfStudy[]
     */
    public function findByDepartment(Department $department): array
    {
        return FieldOfStudy::where('department_id', $department->id)
            ->get()
            ->toArray();
    }
}

