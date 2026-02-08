<?php

namespace App\Entity\Repository;

use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityRepository;

class ChangeLogItemRepository extends EntityRepository
{
    /**
     * @return ChangeLogItem[]
     */
    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('change_log_item')
            ->select('change_log_item')
            ->orderBy("change_log_item.date")
            ->getQuery()
            ->getResult();
    }
}
