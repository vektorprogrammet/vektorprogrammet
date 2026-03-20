<?php

namespace App\Admission\Infrastructure\Repository;

use App\Admission\Infrastructure\Entity\AdmissionSubscriber;
use App\Entity\Department;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class AdmissionSubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmissionSubscriber::class);
    }

    /**
     * @return QueryBuilder
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
     * @return QueryBuilder
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
