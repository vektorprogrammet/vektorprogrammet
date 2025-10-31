<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Department;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface for Department repository operations.
 * This interface defines the contract for department data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface DepartmentRepositoryInterface
{
    /**
     * Find all departments.
     *
     * @return Department[]
     */
    public function findAllDepartments(): array;

    /**
     * Find department by ID.
     *
     * @param int $id
     * @return Department[]
     */
    public function findDepartmentById($id): array;

    /**
     * Find all departments with active admission periods.
     *
     * @return Department[]
     */
    public function findAllWithActiveAdmission(): array;

    /**
     * Find department by short name (case-insensitive).
     *
     * @param string $shortName
     * @return Department|null
     */
    public function findDepartmentByShortName($shortName): ?Department;

    /**
     * Create query builder for active departments.
     *
     * @return QueryBuilder
     */
    public function queryForActive(): QueryBuilder;

    /**
     * Find all active departments.
     *
     * @return Department[]
     */
    public function findActive(): array;

    /**
     * Find one department by city (case-insensitive).
     *
     * @param string $city
     * @return Department|null
     */
    public function findOneByCityCaseInsensitive($city): ?Department;
}
