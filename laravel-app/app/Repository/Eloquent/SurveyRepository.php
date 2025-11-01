<?php

namespace App\Repository\Eloquent;

use App\Models\Semester;
use App\Models\Survey;
use App\Models\User;
use App\Repository\Contract\SurveyRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of SurveyRepositoryInterface.
 */
class SurveyRepository implements SurveyRepositoryInterface
{
    /**
     * Find all surveys not taken by user and semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return Survey[]
     */
    public function findAllNotTakenByUserAndSemester(User $user, Semester $semester): array
    {
        $department = $user->getDepartment();
        if (!$department) {
            return [];
        }

        // Get survey IDs that the user has already taken
        $takenSurveyIds = DB::table('survey_taken')
            ->where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        // Find surveys that:
        // 1. Are team surveys (targetAudience = 1, which is TEAM_SURVEY constant)
        // 2. Match the semester
        // 3. Match the department or have no department
        // 4. User hasn't taken yet
        $query = Survey::query()
            ->where('target_audience', 1) // 1 = TEAM_SURVEY constant
            ->where('semester_id', $semester->id)
            ->where(function ($query) use ($department) {
                $query->where('department_id', $department->id)
                      ->orWhereNull('department_id');
            });

        if (!empty($takenSurveyIds)) {
            $query->whereNotIn('id', $takenSurveyIds);
        }

        return $query->get()->toArray();
    }
}

