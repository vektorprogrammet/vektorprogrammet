<?php

namespace App\Repository\Contract;

use App\Models\Semester;
use App\Models\Survey;
use App\Models\User;

/**
 * Interface for Survey repository operations.
 * This interface defines the contract for survey data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SurveyRepositoryInterface
{
    /**
     * Find all surveys not taken by user and semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return Survey[]
     */
    public function findAllNotTakenByUserAndSemester(User $user, Semester $semester): array;
}

