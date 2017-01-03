<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;

/**
 * TeamRepository.
 */
class TeamRepository extends EntityRepository
{
    /**
     * @param Department $department
     *
     * @return Team[]
     */
    public function findByDepartment(Department $department): array
    {
        return $this->createQueryBuilder('team')
            ->select('team')
            ->where('team.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
