<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Department;
use App\Models\User;
use App\Repository\Contract\ApplicationRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of ApplicationRepositoryInterface.
 */
class ApplicationRepository implements ApplicationRepositoryInterface
{
    /**
     * Find application by user and admission period.
     *
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     * @return Application|null
     */
    public function findByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod): ?Application
    {
        return Application::where('user_id', $user->id)
            ->where('admission_period_id', $admissionPeriod->id)
            ->first();
    }

    /**
     * Find active application by user.
     *
     * @param User $user
     * @return Application|null
     */
    public function findActiveByUser(User $user): ?Application
    {
        $department = $user->getDepartment();
        if (!$department) {
            return null;
        }

        $admissionPeriod = $department->getCurrentOrLatestAdmissionPeriod();
        if (!$admissionPeriod) {
            return null;
        }

        return $this->findByUserInAdmissionPeriod($user, $admissionPeriod);
    }

    /**
     * Find emails by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function findEmailsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array
    {
        return Application::query()
            ->where('admission_period_id', $admissionPeriod->id)
            ->join('user', 'application.user_id', '=', 'user.id')
            ->pluck('user.email')
            ->toArray();
    }

    /**
     * Find applications by email and admission period.
     *
     * @param string $email
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findByEmailAndAdmissionPeriod(string $email, AdmissionPeriod $admissionPeriod): array
    {
        return Application::query()
            ->where('admission_period_id', $admissionPeriod->id)
            ->whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->get()
            ->toArray();
    }

    /**
     * Find all applications by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array
    {
        return Application::where('admission_period_id', $admissionPeriod->id)
            ->get()
            ->toArray();
    }

    /**
     * Find all applications by user.
     *
     * @param User $user
     * @return Application[]
     */
    public function findByUser(User $user): array
    {
        return Application::where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Find applications by department and semester.
     *
     * @param Department $department
     * @param \App\Models\Semester $semester
     * @return Application[]
     */
    public function findByDepartmentAndSemester(Department $department, $semester): array
    {
        return Application::query()
            ->whereHas('admissionPeriod', function ($query) use ($department, $semester) {
                $query->where('department_id', $department->id)
                      ->where('semester_id', $semester->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Count applications by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return int
     */
    public function countByAdmissionPeriod(AdmissionPeriod $admissionPeriod): int
    {
        return Application::where('admission_period_id', $admissionPeriod->id)->count();
    }

    /**
     * Find applications with interviews by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findWithInterviewsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array
    {
        return Application::query()
            ->where('admission_period_id', $admissionPeriod->id)
            ->has('interview')
            ->get()
            ->toArray();
    }

    /**
     * Find applications without interviews by admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Application[]
     */
    public function findWithoutInterviewsByAdmissionPeriod(AdmissionPeriod $admissionPeriod): array
    {
        return Application::query()
            ->where('admission_period_id', $admissionPeriod->id)
            ->doesntHave('interview')
            ->get()
            ->toArray();
    }
}

