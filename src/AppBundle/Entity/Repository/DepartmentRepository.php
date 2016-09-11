<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

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

    public function findDepartmentByShortName($short_name){
         $departments = $this->getEntityManager()->createQuery('
            SELECT d
            FROM AppBundle:Department d
            WHERE lower(d.short_name) = lower(:short_name)
        ')
        ->setParameter('short_name', $short_name)
        ->getResult();

        return $departments[0];
    }

    public function findAllDepartment()
    {
        $this->createQueryBuilder('Department')
            ->select('Department')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
