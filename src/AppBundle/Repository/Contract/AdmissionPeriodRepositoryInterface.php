<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for AdmissionPeriod repository operations.
 * This interface defines the contract for admission period data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AdmissionPeriodRepositoryInterface
{
    /**
     * Find admission periods by department, ordered by time.
     *
     * @param Department $department
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentOrderedByTime(Department $department): array;

    /**
     * Find admission periods by department, time, and year.
     *
     * @param Department $department
     * @param string $time
     * @param string $year
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentAndTime(Department $department, string $time, string $year): array;

    /**
     * Find one admission period by department and semester.
     *
     * @param Department $department
     * @param Semester $semester
     * @return AdmissionPeriod|null
     * @throws NonUniqueResultException
     */
    public function findOneByDepartmentAndSemester(Department $department, Semester $semester): ?AdmissionPeriod;

    /**
     * Find one admission period with active admission by department.
     *
     * @param Department $department
     * @param DateTime|null $time
     * @return AdmissionPeriod|null
     * @throws NonUniqueResultException
     */
    public function findOneWithActiveAdmissionByDepartment(Department $department, ?DateTime $time = null): ?AdmissionPeriod;
}
