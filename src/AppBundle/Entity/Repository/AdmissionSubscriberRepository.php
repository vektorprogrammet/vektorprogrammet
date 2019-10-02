<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class AdmissionSubscriberRepository extends EntityRepository
{

    /**
     * @param Department $department
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function findByDepartmentQueryBuilder(Department $department)
    {
        return $this
            ->createQueryBuilder('subscriber')
            ->select('subscriber')
            ->where('subscriber.department = :department')
            ->setParameter('department', $department);
    }
    /**
     * @param Department $department
     *
     * @return AdmissionSubscriber[]
     */
    public function findByDepartment(Department $department)
    {
        return $this
            ->findByDepartmentQueryBuilder($department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartment(Department $department)
    {
        return $this
            ->findByDepartmentQueryBuilder($department)
            ->andWhere('subscriber.fromApplication = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     * @param Semester $semester
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function findByDepartmentAndSemesterQueryBuilder(Department $department, Semester $semester)
    {
        return $this
            ->createQueryBuilder('subscriber')
            ->select('subscriber')
            ->where('subscriber.department = :department')
            ->andWhere('subscriber.timestamp > :semesterStart')
            ->andWhere('subscriber.timestamp < :semesterEnd')
            ->setParameter('department', $department)
            ->setParameter('semesterStart', $semester->getStartDate())
            ->setParameter('semesterEnd', $semester->getEndDate());
    }

    /**
     * @param Department $department
     * @param Semester $semester
     *
     * @return AdmissionSubscriber[]
     */
    public function findFromWebByDepartmentAndSemester(Department $department, Semester $semester)
    {
        return $this
            ->findByDepartmentAndSemesterQueryBuilder($department, $semester)
            ->andWhere('subscriber.department = :department')
            ->andWhere('subscriber.fromApplication = false')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $email
     * @param Department $department
     *
     * @return AdmissionSubscriber
     */
    public function findByEmailAndDepartment(string $email, Department $department)
    {
        return $this
            ->createQueryBuilder('subscriber')
            ->select('subscriber')
            ->where('subscriber.email = :email')
            ->andWhere('subscriber.department = :department')
            ->setParameter('email', $email)
            ->setParameter('department', $department)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     *
     * @return AdmissionSubscriber
     */
    public function findByUnsubscribeCode(string $code)
    {
        return $this
            ->createQueryBuilder('subscriber')
            ->where('subscriber.unsubscribeCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
