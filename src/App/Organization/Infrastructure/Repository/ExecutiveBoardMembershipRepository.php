<?php

declare(strict_types=1);

namespace App\Organization\Infrastructure\Repository;

use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExecutiveBoardMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExecutiveBoardMembership::class);
    }

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
