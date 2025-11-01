<?php

namespace App\Repository\Contract;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\User;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for Application repository operations.
 * This interface defines the contract for application data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ApplicationRepositoryInterface
{
    /**
     * Find application by user and admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return Application|null
     * @throws NonUniqueResultException
     */
    public function findByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod);

    /**
     * Find active application by user.
     *
     * @param User $user
     * @return Application|null
     * @throws NonUniqueResultException
     */
    public function findActiveByUser(User $user);

    /**
     * Find emails by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function findEmailsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find applications by email and admission period.
     *
     * @param string $email
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findByEmailInAdmissionPeriod($email, AdmissionPeriod $admissionPeriod): array;

    /**
     * Find all interviewed applicants.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findInterviewedApplicants($admissionPeriod): array;

    /**
     * Find previous applicants.
     *
     * @param Department|null $department
     * @param AdmissionPeriod|null $admissionPeriod
     * @return Application[]
     */
    public function findPreviousApplicants($department = null, $admissionPeriod = null): array;

    /**
     * Find assigned applicants.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findAssignedApplicants($admissionPeriod): array;

    /**
     * Find assigned applications by user and admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findAssignedByUserAndAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod): array;

    /**
     * Find cancelled applicants.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findCancelledApplicants(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find new applicants.
     *
     * @param Department|null $department
     * @param AdmissionPeriod|null $admissionPeriod
     * @return Application[]
     */
    public function findNewApplicants($department = null, $admissionPeriod = null): array;

    /**
     * Find new applications by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function findNewApplicationsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find existing applicants.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findExistingApplicants(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find applications by team interest and admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function findApplicationByTeamInterestAndAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find applicant by ID.
     *
     * @param int $id
     * @return Application
     * @throws NonUniqueResultException
     */
    public function findApplicantById($id): Application;

    /**
     * Find applicant statistic by ID.
     *
     * @param int $id
     * @return object ApplicationStatistic entity
     * @throws NonUniqueResultException
     */
    public function findApplicantStatisticById($id): object;

    /**
     * Get number of applications.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return int
     */
    public function numOfApplications(AdmissionPeriod $admissionPeriod): int;

    /**
     * Get number of applications by gender.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @param int $gender
     * @return int
     */
    public function numOfGender(AdmissionPeriod $admissionPeriod, $gender): int;

    /**
     * Get number of previous participations.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return int
     */
    public function numOfPreviousParticipation(AdmissionPeriod $admissionPeriod): int;

    /**
     * Get number of accepted applications.
     *
     * @param bool $accepted
     * @return int
     */
    public function numOfAccepted($accepted): int;

    /**
     * Get number of applications by year of study.
     *
     * @param int $yearOfStudy
     * @return int
     */
    public function numOfYearOfStudy($yearOfStudy): int;

    /**
     * Get number of applications by field of study.
     *
     * @param string $fieldOfStudy
     * @return int
     */
    public function numOfFieldOfStudy($fieldOfStudy): int;

    /**
     * Get number of applications by department.
     *
     * @param int $department
     * @return int
     */
    public function numOfDepartment($department): int;

    /**
     * Find all allocatable applications by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findAllAllocatableApplicationsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find substitutes by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findSubstitutesByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find applications by department.
     *
     * @param Department $department
     * @return Application[]
     */
    public function findByDepartment(Department $department): array;

    /**
     * Find applications by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;
}
