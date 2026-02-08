<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use App\Entity\SchoolCapacity;
use App\Entity\Semester;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SchoolCapacityRepository extends EntityRepository
{

    /**
     * @param Department $school
     * @param Semester $semester
     *
     * @return SchoolCapacity
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findBySchoolAndSemester($school, $semester)
    {
        $schoolCapacities = $this->getEntityManager()->createQuery('
		SELECT sc
		FROM App\Entity\SchoolCapacity sc
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
