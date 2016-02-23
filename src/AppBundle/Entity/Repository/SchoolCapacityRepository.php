<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SchoolCapacityRepository extends EntityRepository {

	public function findBySchoolAndSemester($school, $semester){
        $schoolCapacities =  $this->getEntityManager()->createQuery("
		SELECT sc
		FROM AppBundle:SchoolCapacity sc
		WHERE sc.school = :school
		AND sc.semester = :semester
		")
            ->setParameter('school', $school)
            ->setParameter('semester', $semester)
            ->getSingleResult();

        return $schoolCapacities;
    }
	
}
