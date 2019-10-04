<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ChangeLogItem;
use Doctrine\ORM\EntityRepository;


class ParentAssignmentRepository extends EntityRepository
{
    public function findAllParents()
    {
        return $this->createQueryBuilder('parent_assignment')
            ->select('parent_assignment')
            ->orderBy("date")
            ->getQuery()
            ->getResult();
    }
}
