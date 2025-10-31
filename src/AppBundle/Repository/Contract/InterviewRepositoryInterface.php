<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Interview;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use DateTime;

/**
 * Interface for Interview repository operations.
 * This interface defines the contract for interview data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface InterviewRepositoryInterface
{
    /**
     * Find last scheduled interview by user in admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return Interview|null
     */
    public function findLastScheduledByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod): ?Interview;

    /**
     * Find all interviewed interviews by semester.
     *
     * @param Semester $semester
     * @return Interview[]
     */
    public function findAllInterviewedInterviewsBySemester(Semester $semester): array;

    /**
     * Get number of interviews by user in semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return int
     */
    public function numberOfInterviewsByUserInSemester(User $user, Semester $semester): int;

    /**
     * Find latest interview by user.
     *
     * @param User $user
     * @return Interview|null
     */
    public function findLatestInterviewByUser(User $user): ?Interview;

    /**
     * Find interview by response code.
     *
     * @param string $responseCode
     * @return Interview|null
     */
    public function findByResponseCode(string $responseCode): ?Interview;

    /**
     * Find uncompleted interviews by interviewer in current semester.
     *
     * @param User $interviewer
     * @return Interview[]
     */
    public function findUncompletedInterviewsByInterviewerInCurrentSemester(User $interviewer): array;

    /**
     * Find interviewers in semester.
     *
     * @param Semester $semester
     * @return User[]
     */
    public function findInterviewersInSemester(Semester $semester): array;

    /**
     * Find interviews which will receive accept-interview notifications.
     * All interviews scheduled to a time after $time and having PENDING
     * interview status apply.
     *
     * @param DateTime $time
     * @return Interview[]
     */
    public function findAcceptInterviewNotificationRecipients(DateTime $time): array;
}

