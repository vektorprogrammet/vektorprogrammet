<?php

namespace App\Scheduling\Infrastructure\Repository;

use App\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    /**
     * @return School[]
     */
    public function findActiveSchoolsByDepartment(Department $department)
    {
        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return School[]
     */
    public function findInactiveSchoolsByDepartment(Department $department)
    {
        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findActiveSchoolsWithoutCapacity(Department $department)
    {
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
            ->select('IDENTITY(capacity.school)')
            ->from(SchoolCapacity::class, 'capacity')
            ->where('capacity.semester = :semester');

        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->setParameter('semester', $department->getCurrentAdmissionPeriod()->getSemester())
            ->andWhere($qb->expr()->notIn('school.id', $exclude->getDQL()));
    }

    /**
     * @return QueryBuilder
     */
    private function getSchoolsByDepartmentQueryBuilder(Department $department)
    {
        return $this->createQueryBuilder('school')
            ->select('school')
            ->join('school.departments', 'departments')
            ->where('departments = :department')
            ->setParameter('department', $department);
    }
}
