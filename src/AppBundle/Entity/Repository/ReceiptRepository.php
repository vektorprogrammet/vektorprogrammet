<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Receipt;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class ReceiptRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        /*
         * @param User $user
         *
         * @return Receipt[]
         */
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

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
