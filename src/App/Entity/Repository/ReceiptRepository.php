<?php

namespace App\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Receipt;

class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receipt::class);
    }

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
