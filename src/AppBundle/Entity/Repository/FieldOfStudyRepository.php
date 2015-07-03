<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class FieldOfStudyRepository extends EntityRepository {

	/*	Perhaps not needed anymore?

	public function findFieldOfStudyByName($short_name){
		$stmt = $this->getEntityManager()
					->getConnection()
                   ->prepare('
					SELECT *
					FROM Field_of_study F
					WHERE short_name = :short_name
					');
		
		$stmt->bindValue('short_name', $short_name);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
<<<<<<< Updated upstream
	*/

    public function findAllFieldOfStudy(){
        return $this->createQueryBuilder('FieldOfStudy')
            ->select('FieldOfStudy')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}