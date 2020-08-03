<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * AdmissionPeriodRepository
 */
class AdmissionPeriodRepository extends EntityRepository
{

    /**
     * @param Department $department
     *
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentOrderedByTime(Department $department): array
    {
        return $this->createQueryBuilder('ap')
            ->where('ap.department = :department')
            ->join('ap.semester', 's')
            ->addOrderBy('s.year', 'DESC')
            ->addOrderBy('s.semesterTime', 'ASC')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     * @param string     $time
     * @param string     $year
     *
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentAndTime(Department $department, string $time, string $year): array
    {
        return $this->createQueryBuilder('dss')
            ->where('dss.department = :department')
            ->join('semester', 's')
            ->andWhere('s.semesterTime = :time')
            ->andWhere('s.year = :year')
            ->setParameter('department', $department)
            ->setParameter('time', $time)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     * @param Semester $semester
     *
     * @return AdmissionPeriod|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByDepartmentAndSemester(Department $department, Semester $semester): ?AdmissionPeriod
    {
        return $this->createQueryBuilder('admissionPeriod')
            ->where('admissionPeriod.department = :department')
            ->andWhere('admissionPeriod.semester = :semester')
            ->setParameter('department', $department)
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Department $department
     * @param DateTime   $time
     *
     * @return AdmissionPeriod|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneWithActiveAdmissionByDepartment(Department $department, DateTime $time = null): ?AdmissionPeriod
    {
        if ($time === null) {
            $time = new DateTime();
        }

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.department = ?1')
            ->andWhere('Semester.admissionStartDate <= :time')
            ->andWhere('Semester.admissionEndDate >= :time')
            ->setParameter(1, $department)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
