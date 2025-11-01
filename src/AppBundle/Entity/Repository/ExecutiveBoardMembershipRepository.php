<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ExecutiveBoardMembershipRepository extends EntityRepository implements ExecutiveBoardMembershipRepositoryInterface
{
    /**
     * @param User $user
     * @return ExecutiveBoardMembership[]
     */
    public function findByUser(User $user): array
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
