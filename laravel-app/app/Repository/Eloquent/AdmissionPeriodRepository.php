<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Models\Semester;
use App\Repository\Contract\AdmissionPeriodRepositoryInterface;
use Carbon\Carbon;
use DateTime;

/**
 * Eloquent implementation of AdmissionPeriodRepositoryInterface.
 */
class AdmissionPeriodRepository implements AdmissionPeriodRepositoryInterface
{
    /**
     * Find admission periods by department, ordered by time.
     *
     * @param Department $department
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentOrderedByTime(Department $department): array
    {
        return AdmissionPeriod::query()
            ->where('department_id', $department->id)
            ->with('semester')
            ->join('semester', 'admission_period.semester_id', '=', 'semester.id')
            ->orderBy('semester.year', 'desc')
            ->orderByRaw("CASE WHEN semester.semester_time = 'Vår' THEN 1 WHEN semester.semester_time = 'Høst' THEN 2 END")
            ->select('admission_period.*')
            ->get()
            ->toArray();
    }

    /**
     * Find admission periods by department, time, and year.
     *
     * @param Department $department
     * @param string $time
     * @param string $year
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentAndTime(Department $department, string $time, string $year): array
    {
        return AdmissionPeriod::query()
            ->where('department_id', $department->id)
            ->whereHas('semester', function ($query) use ($time, $year) {
                $query->where('semester_time', $time)
                      ->where('year', $year);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find one admission period by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AdmissionPeriod|null
     */
    public function findOneByDepartmentAndSemester(Department $department, Semester $semester): ?AdmissionPeriod
    {
        return AdmissionPeriod::where('department_id', $department->id)
            ->where('semester_id', $semester->id)
            ->first();
    }

    /**
     * Find one admission period with active admission by department.
     *
     * @param Department $department
     * @param DateTime|null $time
     * @return AdmissionPeriod|null
     */
    public function findOneWithActiveAdmissionByDepartment(Department $department, ?DateTime $time = null): ?AdmissionPeriod
    {
        if ($time === null) {
            $time = Carbon::now();
        } else {
            $time = Carbon::instance($time);
        }

        return AdmissionPeriod::where('department_id', $department->id)
            ->where('start_date', '<=', $time)
            ->where('end_date', '>=', $time)
            ->first();
    }
}

