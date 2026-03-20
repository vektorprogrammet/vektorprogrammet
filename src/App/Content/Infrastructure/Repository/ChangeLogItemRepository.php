<?php

namespace App\Content\Infrastructure\Repository;

use App\Content\Infrastructure\Entity\ChangeLogItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ChangeLogItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChangeLogItem::class);
    }

    /**
     * @return ChangeLogItem[]
     */
    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('change_log_item')
            ->select('change_log_item')
            ->orderBy('change_log_item.date')
            ->getQuery()
            ->getResult();
    }
}
