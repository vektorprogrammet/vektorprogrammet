<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ExecutiveBoard;
use Doctrine\ORM\EntityRepository;

class ExecutiveBoardRepository extends EntityRepository
{
    public function findBoard(): ExecutiveBoard
    {
        return $this->createQueryBuilder('board')
            ->getQuery()
            ->getSingleResult();
    }
}
