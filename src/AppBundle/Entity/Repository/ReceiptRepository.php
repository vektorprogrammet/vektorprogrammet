<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\ReceiptRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ReceiptRepository extends EntityRepository implements ReceiptRepositoryInterface
{
    /**
     * @param User $user
     *
     * @return Receipt[]
     */
    public function findByUser(User $user): array
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
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}
