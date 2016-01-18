<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityRepository;

class SemesterRepository extends EntityRepository {

    public function findNameById($id){
        return $this->createQueryBuilder('Semester')
            ->select('Semester.name')
            ->where('Semester.id = '.$id)
            ->getQuery()
            ->getSingleScalarResult();
    }

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

    public function findLatestSemesterByDepartmentId($departmentId){
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = :department')
            ->setParameter('department', $departmentId)
            ->orderBy('Semester.semesterEndDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param Department $department
     * @param DateTime $time
     * @return Department
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSemesterWithActiveAdmissionByDepartment(Department $department, DateTime $time = null){
        if($time === null) $time = new \DateTime();
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = ?1')
            ->andWhere('Semester.admission_start_date < :time')
            ->andWhere('Semester.admission_end_date > :time')
            ->setParameter(1, $department)
            ->setParameter('time', $time)
            ->getQuery()
            ->getSingleResult();
    }


}