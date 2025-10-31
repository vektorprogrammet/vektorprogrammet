<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\Team;

/**
 * Interface for Team repository operations.
 * This interface defines the contract for team data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface TeamRepositoryInterface
{
    /**
     * Find teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findByDepartment(Department $department): array;

    /**
     * Find active teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findActiveByDepartment(Department $department): array;

    /**
     * Find inactive teams by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findInActiveByDepartment(Department $department): array;

    /**
     * Find teams with open application by department.
     *
     * @param Department $department
     * @return Team[]
     */
    public function findByOpenApplicationAndDepartment(Department $department): array;

    /**
     * Find all team emails.
     *
     * @return string[]
     */
    public function findAllEmails(): array;

    /**
     * Find teams by team interest and admission period.
     *
     * @param AdmissionPeriod $admissionPeriod
     * @return Team[]
     */
    public function findByTeamInterestAndAdmissionPeriod(AdmissionPeriod $admissionPeriod): array;

    /**
     * Find team by city and name.
     *
     * @param string $departmentCity
     * @param string $name
     * @return Team[]
     */
    public function findByCityAndName(string $departmentCity, string $name): array;
}

