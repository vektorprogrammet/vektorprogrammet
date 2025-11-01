<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\Semester;
use App\Models\SocialEvent;
use App\Repository\Contract\SocialEventRepositoryInterface;
use Carbon\Carbon;

/**
 * Eloquent implementation of SocialEventRepositoryInterface.
 */
class SocialEventRepository implements SocialEventRepositoryInterface
{
    /**
     * Find social events by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array
    {
        return SocialEvent::query()
            ->where(function ($query) use ($semester) {
                $query->where('semester_id', $semester->id)
                      ->orWhereNull('semester_id');
            })
            ->where(function ($query) use ($department) {
                $query->where('department_id', $department->id)
                      ->orWhereNull('department_id');
            })
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }

    /**
     * Find future social events by semester and department.
     *
     * @param Semester $semester
     * @param Department $department
     * @return SocialEvent[]
     */
    public function findFutureSocialEventsBySemesterAndDepartment(Semester $semester, Department $department): array
    {
        $now = Carbon::now();

        return SocialEvent::query()
            ->where(function ($query) use ($semester) {
                $query->where('semester_id', $semester->id)
                      ->orWhereNull('semester_id');
            })
            ->where(function ($query) use ($department) {
                $query->where('department_id', $department->id)
                      ->orWhereNull('department_id');
            })
            ->where('start_time', '>=', $now)
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }
}

