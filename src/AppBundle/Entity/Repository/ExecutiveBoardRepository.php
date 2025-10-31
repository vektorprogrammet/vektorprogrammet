<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Repository\Contract\ExecutiveBoardRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ExecutiveBoardRepository extends EntityRepository implements ExecutiveBoardRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findBoard(): ExecutiveBoard
    {
        return $this->createQueryBuilder('board')
            ->getQuery()
            ->getSingleResult();
    }
}
