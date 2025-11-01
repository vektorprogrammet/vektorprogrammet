<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ChangeLogItem;
use AppBundle\Repository\Contract\ChangeLogItemRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ChangeLogItemRepository extends EntityRepository implements ChangeLogItemRepositoryInterface
{
    /**
     * @return ChangeLogItem[]
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('change_log_item')
            ->select('change_log_item')
            ->orderBy("change_log_item.date")
            ->getQuery()
            ->getResult();
    }
}
