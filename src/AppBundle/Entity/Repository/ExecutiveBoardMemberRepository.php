<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ExecutiveBoardMemberRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('bm')
            ->where('bm.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
