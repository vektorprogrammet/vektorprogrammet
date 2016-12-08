<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Team;
use AppBundle\Entity\TeamApplication;
use Doctrine\ORM\EntityRepository;

/**
 * TeamApplicationRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TeamApplicationRepository extends EntityRepository
{
    /**
     * @param Team $team
     *
     * @return TeamApplication[]
     */
    public function findByTeam(Team $team)
    {
        return $this->createQueryBuilder('teamApplication')
            ->select('teamApplication')
            ->where('teamApplication.team = :team')
            ->setParameter('team', $team)
            ->getQuery()
            ->getResult();
    }
}
