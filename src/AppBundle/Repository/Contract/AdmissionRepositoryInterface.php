<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Admission;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for Admission repository operations.
 * This interface defines the contract for admission data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AdmissionRepositoryInterface
{
    /**
     * Find interviewed applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findInterviewedApplicants($department = null, $semester = null): array;

    /**
     * Find interviewed applicants by interviewer.
     *
     * @param Department $department
     * @param Semester $semester
     * @param User $interviewer
     * @return Admission[]
     */
    public function findInterviewedApplicantsByInterviewer($department, $semester, $interviewer): array;

    /**
     * Find assigned applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findAssignedApplicants($department = null, $semester = null): array;

    /**
     * Find new applicants.
     *
     * @param Department|null $department
     * @param Semester|null $semester
     * @return Admission[]
     */
    public function findNewApplicants($department = null, $semester = null): array;

    /**
     * Find applicant by ID.
     *
     * @param int $id
     * @return Admission
     * @throws NonUniqueResultException
     */
    public function findApplicantById($id): Admission;

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
     * @return int
     */
    public function numOfApplications(): int;

    /**
     * Get number of semesters.
     *
     * @return int
     */
    public function numOfSemesters(): int;

    /**
     * Get number of applications by semester.
     *
     * @param Semester $semester
     * @return int
     */
    public function numOfSemester($semester): int;

    /**
     * Get number of applications by gender.
     *
     * @param int $gender
     * @return int
     */
    public function numOfGender($gender): int;

    /**
     * Get number of previous participations.
     *
     * @param bool $participated
     * @return int
     */
    public function numOfPreviousParticipation($participated): int;

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
}

