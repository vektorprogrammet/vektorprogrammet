<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Entity\Semester;
use AppBundle\Repository\Contract\SchoolCapacityRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SchoolCapacityRepository extends EntityRepository implements SchoolCapacityRepositoryInterface
{
    /**
     * @param School $school
     * @param Semester $semester
     *
     * @return SchoolCapacity
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findBySchoolAndSemester($school, $semester): SchoolCapacity
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
    public function findByDepartmentAndSemester(Department $department, Semester $semester): array
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
