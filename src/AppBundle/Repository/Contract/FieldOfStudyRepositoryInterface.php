<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;

/**
 * Interface for FieldOfStudy repository operations.
 * This interface defines the contract for field of study data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface FieldOfStudyRepositoryInterface
{
    /**
     * Find all fields of study.
     *
     * @return FieldOfStudy[]
     */
    public function findAllFieldOfStudy(): array;

    /**
     * Find fields of study by department.
     *
     * @param Department $department
     * @return FieldOfStudy[]
     */
    public function findByDepartment(Department $department): array;
}

