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
        return $receipts = $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
