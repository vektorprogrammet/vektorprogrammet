<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Repository\Contract\AdmissionSubscriberRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmissionSubscriberRepository extends EntityRepository implements AdmissionSubscriberRepositoryInterface
{

    /**
     * @param Department $department
     *
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
     * {@inheritdoc}
     */
    public function findByDepartment(Department $department): array
    {
        return $this
            ->findByDepartmentQueryBuilder($department)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findFromWebByDepartment(Department $department): array
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
     * {@inheritdoc}
     */
    public function findFromWebByDepartmentAndSemester(Department $department, Semester $semester): array
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
     * {@inheritdoc}
     */
    public function findByEmailAndDepartment(string $email, Department $department): ?AdmissionSubscriber
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
     * {@inheritdoc}
     */
    public function findByUnsubscribeCode(string $code): ?AdmissionSubscriber
    {
        return $this
            ->createQueryBuilder('subscriber')
            ->where('subscriber.unsubscribeCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
