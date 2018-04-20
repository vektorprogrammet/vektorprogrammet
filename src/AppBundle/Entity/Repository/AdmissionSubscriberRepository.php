<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;

class AdmissionSubscriberRepository extends EntityRepository
{

    /**
     * @param Department $department
     *
     * @return AdmissionSubscriber[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('subscriber')
                    ->select('subscriber')
                    ->andWhere('subscriber.department = :department')
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
        return $this->createQueryBuilder('subscriber')
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
        return $this->createQueryBuilder('subscriber')
            ->where('subscriber.unsubscribeCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
