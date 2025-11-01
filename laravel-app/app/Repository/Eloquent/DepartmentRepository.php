<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Repository\Contract\DepartmentRepositoryInterface;

/**
 * Eloquent implementation of DepartmentRepositoryInterface.
 */
class DepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * Find all departments.
     *
     * @return Department[]
     */
    public function findAllDepartments(): array
    {
        return Department::all()->toArray();
    }

    /**
     * Find department by ID.
     *
     * @param int $id
     * @return Department[]
     */
    public function findDepartmentById($id): array
    {
        $department = Department::find($id);
        return $department ? [$department] : [];
    }

    /**
     * Find all departments with active admission periods.
     *
     * @return Department[]
     */
    public function findAllWithActiveAdmission(): array
    {
        return Department::all()->filter(function (Department $department) {
            $admissionPeriod = $department->getCurrentAdmissionPeriod();
            return $admissionPeriod !== null && $admissionPeriod->hasActiveAdmission();
        })->values()->toArray();
    }

    /**
     * Find department by short name (case-insensitive).
     *
     * @param string $shortName
     * @return Department|null
     */
    public function findDepartmentByShortName($shortName): ?Department
    {
        return Department::whereRaw('LOWER(short_name) = LOWER(?)', [$shortName])->first();
    }

    /**
     * Create query builder for active departments.
     * Note: For Laravel, this returns an Eloquent query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryForActive()
    {
        return Department::where('active', true);
    }

    /**
     * Find all active departments.
     *
     * @return Department[]
     */
    public function findActive(): array
    {
        return Department::where('active', true)->get()->toArray();
    }

    /**
     * Find one department by city (case-insensitive).
     *
     * @param string $city
     * @return Department|null
     */
    public function findOneByCityCaseInsensitive($city): ?Department
    {
        return Department::whereRaw('UPPER(city) = UPPER(?)', [$city])->first();
    }
}

