<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Role;
use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class SurveyNotificationRepository extends EntityRepository
{

    /**
     * @param string $identifier
     * @return SurveyNotification?
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserIdentifier(string $identifier) : ?SurveyNotification
    {
        return $this
            ->createQueryBuilder('notif')
            ->where('notif.userIdentifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
