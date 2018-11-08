<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class SchoolCapacityRepository extends EntityRepository
{

    /**
     * @param Department $school
     * @param Semester $semester
     *
     * @return SchoolCapacity
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySchoolAndSemester($school, $semester)
    {
        $schoolCapacities = $this->getEntityManager()->createQuery('
		SELECT sc
		FROM AppBundle:SchoolCapacity sc
		WHERE sc.school = :school
		AND sc.semester = :semester
		')
            ->setParameter('school', $school)
            ->setParameter('semester', $semester)
            ->getSingleResult();

        return $schoolCapacities;
    }

    /**
     * @param Department $department
     * @param Semester $semester
     *
     * @return SchoolCapacity[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester)
    {
        return $this->createQueryBuilder('sc')
            ->select('sc')
            ->where('sc.department = :department')
            ->andWhere('sc.semester = :semester')
            ->setParameters(array(
                'department' => $department,
                'semester' => $semester,
            ))
            ->getQuery()
            ->getResult();
    }
}
