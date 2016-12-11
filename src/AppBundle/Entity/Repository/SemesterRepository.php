<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityRepository;

class SemesterRepository extends EntityRepository
{
    public function findAllSemestersByDepartment($department)
    {
        $semesters = $this->getEntityManager()->createQuery('
		
		SELECT s
		FROM AppBundle:Semester s
		WHERE s.department = :department
		ORDER BY s.semesterStartDate DESC
		
		')
            ->setParameter('department', $department)
            ->getResult();

        return $semesters;
    }

    public function findAllSemesterName()
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester.name')
            ->where('Semester.id = 1')
            ->getQuery()
            ->getScalarResult();
    }

    public function numOfSemesters()
    {
        return $this->createQueryBuilder('Semester')
            ->select('count(Semester.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllSemesters()
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $department
     *
     * @return Semester
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrentSemesterByDepartment($department)
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = ?1')
            ->andWhere('Semester.semesterStartDate < :now')
            ->andWhere('Semester.semesterEndDate > :now')
            ->setParameter(1, $department)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestSemesterByDepartmentId($departmentId)
    {
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
     * @param DateTime   $time
     *
     * @return Semester
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSemesterWithActiveAdmissionByDepartment(Department $department, DateTime $time = null)
    {
        if ($time === null) {
            $time = new \DateTime();
        }

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = ?1')
            ->andWhere('Semester.admissionStartDate < :time')
            ->andWhere('Semester.admissionEndDate > :time')
            ->setParameter(1, $department)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Semester[]
     */
    public function findSemestersWithActiveAdmission()
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.admissionStartDate < :time')
            ->andWhere('Semester.admissionEndDate > :time')
            ->setParameter('time', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
