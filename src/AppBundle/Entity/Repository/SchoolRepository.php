<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SchoolRepository extends EntityRepository {
	
	/*
    public function schoolByName($id){
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('
					SELECT *
					FROM School S
					WHERE id = :id
					');

        $stmt->bindValue('id', $id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
	*/
	public function findSchoolsByDepartment($department){
		
		$schools =  $this->getEntityManager()->createQuery("
		
		SELECT s, d
		FROM AppBundle:School s
		JOIN s.departments d		
		WHERE d = :department
		")
		->setParameter('department', $department)
		->getResult();

		return $schools;
	}

	public function findSchoolsByDepartmentQuery($department){

		$query =  $this->createQueryBuilder('s','d')
			->from('AppBundle:School','s')
			->join('s.departments','d')
			->where('d = :department')
			->setParameter('department',$department);
		return $query;
	}

    public function getNumberOfSchools(){
        $schools =  $this->getEntityManager()->createQuery("

		SELECT COUNT (s.id)
		FROM AppBundle:School s
		")
            ->getSingleScalarResult();

        return $schools;
    }
	
}