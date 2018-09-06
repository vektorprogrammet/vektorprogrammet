<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SchoolRepository extends EntityRepository
{
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
            ->from('AppBundle:SchoolCapacity', 'capacity')
            ->where('capacity.semester = :semester');

        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->setParameter('department', $department)
            ->setParameter('semester', $department->getCurrentSemester())
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
