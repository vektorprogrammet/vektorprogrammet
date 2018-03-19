<?php

namespace AppBundle\Entity\Repository;

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
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $status
     *
     * @return Receipt[]
     */
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}
