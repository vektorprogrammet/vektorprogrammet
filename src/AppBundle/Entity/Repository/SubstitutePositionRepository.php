<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Semester;
use AppBundle\Entity\SubstitutePosition;
use Doctrine\ORM\EntityRepository;

class SubstitutePositionRepository extends EntityRepository
{
    /**
     * @param Semester $semester
     *
     * @return SubstitutePosition[]
     */
    public function findBySemester(Semester $semester)
    {
        return $this->createQueryBuilder('sp')
            ->select('sp')
            ->where('sp.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }
}