<?php

namespace App\Entity\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class ExecutiveBoardMembershipRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('bm')
            ->where('bm.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('bm.startSemester', 's')
            ->addOrderBy('s.semesterTime', 'ASC')
            ->addOrderBy('s.year', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
