<?php

namespace App\Repository\Contract;

use App\Models\AdmissionSubscriber;
use App\Models\Department;
use App\Models\Semester;

/**
 * Interface for AdmissionSubscriber repository operations.
 * This interface defines the contract for admission subscriber data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AdmissionSubscriberRepositoryInterface
{
    /**
     * Find subscribers by department.
     *
     * @param Department $department
     * @return AdmissionSubscriber[]
     */
    public function findByDepartment(Department $department): array;

    /**
     * Find subscribers from web by department.
     *
     * @param Department $department
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartment(Department $department): array;

    /**
     * Find subscribers from web by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartmentAndSemester(Department $department, Semester $semester): array;

    /**
     * Find subscriber by email and department.
     *
     * @param string $email
     * @param Department $department
     * @return AdmissionSubscriber|null
     */
    public function findByEmailAndDepartment(string $email, Department $department): ?AdmissionSubscriber;

    /**
     * Find subscriber by unsubscribe code.
     *
     * @param string $code
     * @return AdmissionSubscriber|null
     */
    public function findByUnsubscribeCode(string $code): ?AdmissionSubscriber;
}

