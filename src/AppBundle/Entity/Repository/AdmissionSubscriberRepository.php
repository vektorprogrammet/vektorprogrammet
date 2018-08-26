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
     * @param Semester $semester
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function findBySemesterQueryBuilder(Semester $semester)
    {
        $department = $semester->getDepartment();

        return $this
            ->createQueryBuilder('subscriber')
            ->select('subscriber')
            ->where('subscriber.department = :department')
            ->andWhere('subscriber.timestamp > :semesterStart')
            ->andWhere('subscriber.timestamp < :semesterEnd')
            ->setParameter('department', $department)
            ->setParameter('semesterStart', $semester->getSemesterStartDate())
            ->setParameter('semesterEnd', $semester->getSemesterEndDate());
    }

    /**
     * @param Semester $semester
     *
     * @return AdmissionSubscriber[]
     *
     */
    public function findBySemester(Semester $semester)
    {
        return $this
            ->findBySemesterQueryBuilder($semester)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Semester $semester
     *
     * @return AdmissionSubscriber[]
     *
     */
    public function findFromWebBySemester(Semester $semester)
    {
        return $this
            ->findBySemesterQueryBuilder($semester)
            ->andWhere('subscriber.fromApplication = false')
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
