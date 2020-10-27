<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\SurveyNotification;
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
