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
}
