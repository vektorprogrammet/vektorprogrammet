<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Semester;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface for Semester repository operations.
 * This interface defines the contract for semester data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SemesterRepositoryInterface
{
    /**
     * Create query builder for all semesters ordered by age.
     *
     * @return QueryBuilder
     */
    public function queryForAllSemestersOrderedByAge(): QueryBuilder;

    /**
     * Find all semesters ordered by age.
     *
     * @return Semester[]
     */
    public function findAllOrderedByAge(): array;

    /**
     * Find current semester.
     *
     * @return Semester|null
     * @throws NonUniqueResultException
     */
    public function findCurrentSemester(): ?Semester;

    /**
     * Find or create current semester.
     *
     * @return Semester
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function findOrCreateCurrentSemester(): Semester;

    /**
     * Find semester by time and year.
     *
     * @param string $semesterTime
     * @param string $year
     * @return Semester|null
     * @throws NonUniqueResultException
     */
    public function findByTimeAndYear(string $semesterTime, string $year): ?Semester;

    /**
     * Get next active semester.
     *
     * @param Semester $semester
     * @return Semester|null
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function getNextActive(Semester $semester): ?Semester;
}
