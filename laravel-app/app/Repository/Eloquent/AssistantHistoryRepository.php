<?php

namespace App\Repository\Eloquent;

use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\School;
use App\Models\Semester;
use App\Models\User;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use Carbon\Carbon;

/**
 * Eloquent implementation of AssistantHistoryRepositoryInterface.
 */
class AssistantHistoryRepository implements AssistantHistoryRepositoryInterface
{
    /**
     * Find assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findByUser(User $user): array
    {
        return AssistantHistory::where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Find most recent assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findMostRecentByUser(User $user): array
    {
        return AssistantHistory::query()
            ->where('user_id', $user->id)
            ->join('semester', 'assistant_history.semester_id', '=', 'semester.id')
            ->orderBy('semester.year', 'desc')
            ->orderByRaw("CASE WHEN semester.semester_time = 'Vår' THEN 1 WHEN semester.semester_time = 'Høst' THEN 2 END")
            ->select('assistant_history.*')
            ->get()
            ->toArray();
    }

    /**
     * Find assistant histories by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AssistantHistory[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        return AssistantHistory::where('department_id', $department->id)
            ->where('semester_id', $semester->id)
            ->get()
            ->toArray();
    }

    /**
     * Find active assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesByUser($user): array
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return AssistantHistory::query()
            ->where('user_id', $user->id)
            ->whereHas('semester', function ($query) use ($year, $semesterTime) {
                $query->where('year', $year)
                      ->where('semester_time', $semesterTime);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find active assistant histories by school.
     *
     * @param School $school
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesBySchool(School $school): array
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return AssistantHistory::query()
            ->where('school_id', $school->id)
            ->whereHas('semester', function ($query) use ($year, $semesterTime) {
                $query->where('year', $year)
                      ->where('semester_time', $semesterTime);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all active assistant histories.
     *
     * @return AssistantHistory[]
     */
    public function findAllActiveAssistantHistories(): array
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return AssistantHistory::query()
            ->whereHas('semester', function ($query) use ($year, $semesterTime) {
                $query->where('year', $year)
                      ->where('semester_time', $semesterTime);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find inactive assistant histories by school.
     *
     * @param School $school
     * @return AssistantHistory[]
     */
    public function findInactiveAssistantHistoriesBySchool(School $school): array
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return AssistantHistory::query()
            ->where('school_id', $school->id)
            ->whereHas('semester', function ($query) use ($year, $semesterTime) {
                $query->where(function ($q) use ($year, $semesterTime) {
                    $q->where('year', '!=', $year)
                      ->orWhere(function ($q2) use ($year, $semesterTime) {
                          $q2->where('year', $year)
                             ->where('semester_time', '!=', $semesterTime);
                      });
                });
            })
            ->get()
            ->toArray();
    }

    /**
     * Get number of female assistants by semester.
     *
     * @param Semester $semester
     * @return int
     */
    public function numFemaleBySemester(Semester $semester): int
    {
        return AssistantHistory::query()
            ->where('semester_id', $semester->id)
            ->whereHas('user', function ($query) {
                $query->where('gender', true); // true = female
            })
            ->count();
    }

    /**
     * Get number of male assistants by semester.
     *
     * @param Semester $semester
     * @return int
     */
    public function numMaleBySemester(Semester $semester): int
    {
        return AssistantHistory::query()
            ->where('semester_id', $semester->id)
            ->whereHas('user', function ($query) {
                $query->where('gender', false); // false = male
            })
            ->count();
    }

    /**
     * Get number of female assistants.
     *
     * @return int
     */
    public function numFemale(): int
    {
        return AssistantHistory::query()
            ->whereHas('user', function ($query) {
                $query->where('gender', true); // true = female
            })
            ->count();
    }

    /**
     * Get number of male assistants.
     *
     * @return int
     */
    public function numMale(): int
    {
        return AssistantHistory::query()
            ->whereHas('user', function ($query) {
                $query->where('gender', false); // false = male
            })
            ->count();
    }

    /**
     * Find all bolk names.
     *
     * @return string[]
     */
    public function findAllBolkNames(): array
    {
        return AssistantHistory::query()
            ->distinct()
            ->whereNotNull('bolk')
            ->pluck('bolk')
            ->unique()
            ->values()
            ->toArray();
    }
}

