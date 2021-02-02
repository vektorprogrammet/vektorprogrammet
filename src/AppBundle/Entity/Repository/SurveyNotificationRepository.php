<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\SurveyNotification;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class SurveyNotificationRepository extends EntityRepository
{

    /**
     * @param string $identifier
     * @return SurveyNotification?
     * @throws NonUniqueResultException
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
