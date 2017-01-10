<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Receipt;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class ReceiptRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        /**
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
            ->andWhere('receipt.isActive = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findInactiveByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->andWhere('receipt.isActive = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
