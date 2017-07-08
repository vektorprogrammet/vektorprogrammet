<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use Doctrine\ORM\EntityRepository;

class SchoolRepository extends EntityRepository
{
    /**
     * @param $department
     *
     * @return School[]
     */
    public function findSchoolsByDepartment($department)
    {
        $schools = $this->getEntityManager()->createQuery('
		
		SELECT s, d
		FROM AppBundle:School s
		JOIN s.departments d		
		WHERE d = :department
		')
            ->setParameter('department', $department)
            ->getResult();

        return $schools;
    }

    public function findSchoolsByDepartmentQuery($department)
    {
        $query = $this->createQueryBuilder('s', 'd')
            ->from('AppBundle:School', 's')
            ->join('s.departments', 'd')
            ->where('d = :department')
            ->setParameter('department', $department);

        return $query;
    }

    public function getNumberOfSchools()
    {
        $schools = $this->getEntityManager()->createQuery('

		SELECT COUNT (s.id)
		FROM AppBundle:School s
		')
            ->getSingleScalarResult();

        return $schools;
    }

    public function findSchoolsWithoutCapacity(Department $department)
    {
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
            ->select('IDENTITY(capacity.school)')
            ->from('AppBundle:SchoolCapacity', 'capacity')
            ->where('capacity.semester = :semester');

        return $this->createQueryBuilder('school')
            ->select('school')
            ->join('school.departments', 'departments')
            ->where('departments = :department')
            ->setParameter('department', $department)
            ->setParameter('semester', $department->getCurrentSemester())
            ->andWhere($qb->expr()->notIn('school.id', $exclude->getDQL()));
    }
}
