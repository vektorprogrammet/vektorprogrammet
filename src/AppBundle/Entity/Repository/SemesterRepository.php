<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class SemesterRepository extends EntityRepository {

	public function findAllSemestersByDepartment($department){
		
		$semesters =  $this->getEntityManager()->createQuery("
		
		SELECT s
		FROM AppBundle:Semester s
		WHERE s.department = :department
		ORDER BY s.semesterStartDate DESC
		
		")
		->setParameter('department', $department)
		->getResult();

		return $semesters;
		
	}

    public function findAllSemesterName(){
        return $this->createQueryBuilder('Semester')
            ->select('Semester.name')
            ->where('Semester.id = 1')
            ->getQuery()
            ->getScalarResult();
    }

    public function NumOfSemesters(){
        return $this->createQueryBuilder('Semester')
            ->select('count(Semester.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllSemesters(){
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $departmentId
     * @return Semester
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrentSemesterByDepartment($departmentId){
        $now = new \DateTime();
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = ?1')
            ->andWhere('Semester.semesterStartDate < :now')
            ->andWhere('Semester.semesterEndDate > :now')
            ->setParameter(1, $departmentId)
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleResult();
    }

	
}