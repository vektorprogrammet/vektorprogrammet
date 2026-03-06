<?php

namespace App\Entity\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function findAllDepartments()
    {
        $departments = $this->getEntityManager()->createQuery('
			SELECT d
			FROM App\Entity\Department d
		')
            ->getResult();

        return $departments;
    }

    public function findDepartmentById($id)
    {
        $departments = $this->getEntityManager()->createQuery('
			SELECT d
			FROM App\Entity\Department d
			WHERE d.id = :id
		')
            ->setParameter('id', $id)
            ->getResult();

        return $departments;
    }

    public function findAllWithActiveAdmission()
    {
        return array_filter($this->findAll(), function (Department $department) {
            $semester = $department->getCurrentAdmissionPeriod();

            return $semester !== null && $semester->hasActiveAdmission();
        });
    }

    public function findDepartmentByShortName($shortName)
    {
        return $this->getEntityManager()->createQuery('
            SELECT d
            FROM App\Entity\Department d
            WHERE lower(d.shortName) = lower(:shortName)
        ')
            ->setParameter('shortName', $shortName)
            ->getOneOrNullResult();
    }

    public function findAllDepartment()
    {
        $this->createQueryBuilder('Department')
            ->select('Department')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function queryForActive()
    {
        return $this->createQueryBuilder('Department')
            ->select('Department')
            ->where('Department.active = true');
    }

    /**
     * @return Department[]
     */
    public function findActive()
    {
        return $this->queryForActive()->getQuery()->getResult();
    }

    public function findOneByCityCaseInsensitive($city)
    {
        return $this->createQueryBuilder('Department')
            ->select('Department')
            ->where('upper(Department.city) = upper(:city)')
            ->setParameter('city', $city)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
