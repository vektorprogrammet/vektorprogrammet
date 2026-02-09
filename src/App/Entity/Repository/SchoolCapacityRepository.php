<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\SchoolCapacity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SchoolCapacityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolCapacity::class);
    }


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
