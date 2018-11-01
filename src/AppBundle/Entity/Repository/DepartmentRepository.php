<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DepartmentRepository extends EntityRepository
{
    public function findAllDepartments()
    {
        $departments = $this->getEntityManager()->createQuery('
			SELECT d
			FROM AppBundle:Department d
		')
            ->getResult();

        return $departments;
    }

    public function findDepartmentById($id)
    {
        $departments = $this->getEntityManager()->createQuery('
			SELECT d
			FROM AppBundle:Department d
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
            FROM AppBundle:Department d
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
