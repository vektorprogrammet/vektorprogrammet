<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\School;
use App\Repository\Contract\SchoolRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of SchoolRepositoryInterface.
 */
class SchoolRepository implements SchoolRepositoryInterface
{
    /**
     * Find active schools by department.
     *
     * @param Department $department
     * @return School[]
     */
    public function findActiveSchoolsByDepartment(Department $department): array
    {
        return School::query()
            ->where('active', true)
            ->whereHas('departments', function ($query) use ($department) {
                $query->where('id', $department->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find inactive schools by department.
     *
     * @param Department $department
     * @return School[]
     */
    public function findInactiveSchoolsByDepartment(Department $department): array
    {
        return School::query()
            ->where('active', false)
            ->whereHas('departments', function ($query) use ($department) {
                $query->where('id', $department->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find active schools without capacity.
     * Note: In Laravel, this returns an Eloquent query builder instance.
     *
     * @param Department $department
     * @return Builder
     */
    public function findActiveSchoolsWithoutCapacity(Department $department): Builder
    {
        $admissionPeriod = $department->getCurrentAdmissionPeriod();
        if (!$admissionPeriod || !$admissionPeriod->semester) {
            // Return empty query if no current admission period
            return School::whereRaw('1 = 0');
        }

        $semester = $admissionPeriod->semester;

        // Get school IDs that have capacity for this semester
        $schoolsWithCapacity = DB::table('school_capacity')
            ->where('semester_id', $semester->id)
            ->pluck('school_id')
            ->toArray();

        $query = School::query()
            ->where('active', true)
            ->whereHas('departments', function ($query) use ($department) {
                $query->where('id', $department->id);
            });

        if (!empty($schoolsWithCapacity)) {
            $query->whereNotIn('id', $schoolsWithCapacity);
        }

        return $query;
    }
}

