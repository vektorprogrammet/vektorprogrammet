<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionSubscriber;
use App\Models\Department;
use App\Models\Semester;
use App\Repository\Contract\AdmissionSubscriberRepositoryInterface;

/**
 * Eloquent implementation of AdmissionSubscriberRepositoryInterface.
 */
class AdmissionSubscriberRepository implements AdmissionSubscriberRepositoryInterface
{
    /**
     * Find subscribers by department.
     *
     * @param Department $department
     * @return AdmissionSubscriber[]
     */
    public function findByDepartment(Department $department): array
    {
        return AdmissionSubscriber::where('department_id', $department->id)
            ->get()
            ->toArray();
    }

    /**
     * Find subscribers from web by department.
     *
     * @param Department $department
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartment(Department $department): array
    {
        return AdmissionSubscriber::where('department_id', $department->id)
            ->where('from_application', false)
            ->get()
            ->toArray();
    }

    /**
     * Find subscribers from web by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartmentAndSemester(Department $department, Semester $semester): array
    {
        $semesterStart = $semester->getStartDate();
        $semesterEnd = $semester->getEndDate();

        return AdmissionSubscriber::query()
            ->where('department_id', $department->id)
            ->where('from_application', false)
            ->where('timestamp', '>', $semesterStart)
            ->where('timestamp', '<', $semesterEnd)
            ->get()
            ->toArray();
    }

    /**
     * Find subscriber by email and department.
     *
     * @param string $email
     * @param Department $department
     * @return AdmissionSubscriber|null
     */
    public function findByEmailAndDepartment(string $email, Department $department): ?AdmissionSubscriber
    {
        return AdmissionSubscriber::where('email', $email)
            ->where('department_id', $department->id)
            ->first();
    }

    /**
     * Find subscriber by unsubscribe code.
     *
     * @param string $code
     * @return AdmissionSubscriber|null
     */
    public function findByUnsubscribeCode(string $code): ?AdmissionSubscriber
    {
        return AdmissionSubscriber::where('unsubscribe_code', $code)->first();
    }
}

