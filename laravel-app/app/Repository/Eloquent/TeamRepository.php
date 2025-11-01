<?php

namespace App\Repository\Eloquent;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Models\Team;
use App\Repository\Contract\TeamRepositoryInterface;

/**
 * Eloquent implementation of TeamRepositoryInterface.
 */
class TeamRepository implements TeamRepositoryInterface
{
    /**
     * Find teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findByDepartment(Department $department): array
    {
        return Team::where('department_id', $department->id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Find active teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findActiveByDepartment(Department $department): array
    {
        return Team::where('department_id', $department->id)
            ->where('active', true)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Find inactive teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findInActiveByDepartment(Department $department): array
    {
        return Team::where('department_id', $department->id)
            ->where('active', false)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Find teams with open application by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findByOpenApplicationAndDepartment(Department $department): array
    {
        return Team::where('department_id', $department->id)
            ->where('accept_application', true)
            ->get()
            ->toArray();
    }

    /**
     * Find all team emails.
     *
     * @return string[]
     */
    public function findAllEmails(): array
    {
        return Team::query()
            ->pluck('email')
            ->toArray();
    }

    /**
     * Find teams by team interest and admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Team[]
     */
    public function findByTeamInterestAndAdmissionPeriod(AdmissionPeriod $admissionPeriod): array
    {
        $semester = $admissionPeriod->semester;
        $department = $admissionPeriod->department;

        if (!$semester || !$department) {
            return [];
        }

        // Find teams that have potential members (applications) for this admission period
        // OR have potential applicants for this semester/department
        return Team::query()
            ->where('department_id', $department->id)
            ->where(function ($query) use ($admissionPeriod, $semester) {
                $query->whereHas('potentialMembers', function ($q) use ($admissionPeriod) {
                    $q->where('admission_period_id', $admissionPeriod->id);
                })
                ->orWhereHas('potentialApplicants', function ($q) use ($semester, $department) {
                    $q->where('semester_id', $semester->id)
                      ->where('department_id', $department->id);
                });
            })
            ->get()
            ->toArray();
    }

    /**
     * Find team by city and name.
     *
     * @param string $departmentCity
     * @param string $name
     * @return Team[]
     */
    public function findByCityAndName(string $departmentCity, string $name): array
    {
        return Team::query()
            ->whereHas('department', function ($query) use ($departmentCity) {
                $query->whereRaw('LOWER(city) = LOWER(?)', [$departmentCity]);
            })
            ->whereRaw('LOWER(name) = LOWER(?)', [$name])
            ->get()
            ->toArray();
    }
}

