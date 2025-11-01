<?php

namespace App\Repository\Contract;

use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\School;
use App\Models\Semester;
use App\Models\User;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for AssistantHistory repository operations.
 * This interface defines the contract for assistant history data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AssistantHistoryRepositoryInterface
{
    /**
     * Find assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findByUser(User $user): array;

    /**
     * Find most recent assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findMostRecentByUser(User $user): array;

    /**
     * Find assistant histories by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AssistantHistory[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array;

    /**
     * Find active assistant histories by user.
     *
     * @param User $user
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesByUser($user): array;

    /**
     * Find active assistant histories by school.
     *
     * @param School $school
     * @return AssistantHistory[]
     */
    public function findActiveAssistantHistoriesBySchool(School $school): array;

    /**
     * Find all active assistant histories.
     *
     * @return AssistantHistory[]
     */
    public function findAllActiveAssistantHistories(): array;

    /**
     * Find inactive assistant histories by school.
     *
     * @param School $school
     * @return AssistantHistory[]
     */
    public function findInactiveAssistantHistoriesBySchool(School $school): array;

    /**
     * Get number of female assistants by semester.
     *
     * @param Semester $semester
     * @return int
     * @throws NonUniqueResultException
     */
    public function numFemaleBySemester(Semester $semester): int;

    /**
     * Get number of male assistants by semester.
     *
     * @param Semester $semester
     * @return int
     * @throws NonUniqueResultException
     */
    public function numMaleBySemester(Semester $semester): int;

    /**
     * Get number of female assistants.
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function numFemale(): int;

    /**
     * Get number of male assistants.
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function numMale(): int;

    /**
     * Find all bolk names.
     *
     * @return string[]
     */
    public function findAllBolkNames(): array;
}
