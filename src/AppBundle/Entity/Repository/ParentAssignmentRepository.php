<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ChangeLogItem;
use Doctrine\ORM\EntityRepository;


class ParentAssignmentRepository extends EntityRepository
{
    public function findAllParentAssignments()
    {
        return $this->createQueryBuilder('parent_assignment')
            ->select('parent_assignment')
            ->orderBy("parent_assignment.tidspunkt", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function getParentAssignmentByUniqueKey(string $key)
    {
        return $this->createQueryBuilder('parent_assignment')
            ->select('parent_assignment')
            ->where('parent_assignment.uniqueKey = :key')
            ->setParameter('key',$key)
            ->getQuery()
            ->getOneOrNullResult();


    }
}
