<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use App\Entity\SchoolCapacity;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class SchoolCapacityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolCapacity::class);
    }

    /**
     * @param Department $school
     * @param Semester   $semester
     *
     * @return SchoolCapacity
     *
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
     * @return SchoolCapacity[]
     */
    public function findByDepartmentAndSemester(Department $department, Semester $semester)
    {
        return $this->createQueryBuilder('sc')
            ->select('sc')
            ->where('sc.department = :department')
            ->andWhere('sc.semester = :semester')
            ->setParameters([
                'department' => $department,
                'semester' => $semester,
            ])
            ->getQuery()
            ->getResult();
    }
}
