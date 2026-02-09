<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    /**
     * @param Department $department
     *
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
     * @param Department $department
     *
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
     * @param Department $department
     *
     * @return QueryBuilder
     */
    public function findActiveSchoolsWithoutCapacity(Department $department)
    {
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
            ->select('IDENTITY(capacity.school)')
            ->from('App\Entity\SchoolCapacity', 'capacity')
            ->where('capacity.semester = :semester');

        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->setParameter('semester', $department->getCurrentAdmissionPeriod()->getSemester())
            ->andWhere($qb->expr()->notIn('school.id', $exclude->getDQL()));
    }

    /**
     * @param Department $department
     *
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
