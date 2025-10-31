<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface for School repository operations.
 * This interface defines the contract for school data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SchoolRepositoryInterface
{
    /**
     * Find active schools by department.
     *
     * @param Department $department
     * @return School[]
     */
    public function findActiveSchoolsByDepartment(Department $department): array;

    /**
     * Find inactive schools by department.
     *
     * @param Department $department
     * @return School[]
     */
    public function findInactiveSchoolsByDepartment(Department $department): array;

    /**
     * Find active schools without capacity.
     *
     * @param Department $department
     * @return QueryBuilder
     */
    public function findActiveSchoolsWithoutCapacity(Department $department): QueryBuilder;
}

