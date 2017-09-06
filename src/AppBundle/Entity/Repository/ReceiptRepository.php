<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;

class ReceiptRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->orderBy('receipt.active', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findActiveByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->andWhere('receipt.active = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findInactiveByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->andWhere('receipt.active = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findActiveByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->andWhere('receipt.active = true')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Receipt[]
     */
    public function findInactiveByDepartment(Department $department)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->join('receipt.user', 'user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('fos.department = :department')
            ->andWhere('receipt.active = false')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
