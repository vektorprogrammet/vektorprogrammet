<?php

namespace App\Entity\Repository;

use App\Entity\ExecutiveBoard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExecutiveBoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExecutiveBoard::class);
    }

    public function findBoard(): ExecutiveBoard
    {
        return $this->createQueryBuilder('board')
            ->getQuery()
            ->getSingleResult();
    }
}
