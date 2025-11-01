<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\SurveyNotification;
use AppBundle\Repository\Contract\SurveyNotificationRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class SurveyNotificationRepository extends EntityRepository implements SurveyNotificationRepositoryInterface
{
    /**
     * @param string $identifier
     * @return SurveyNotification|null
     * @throws NonUniqueResultException
     */
    public function findByUserIdentifier(string $identifier): ?SurveyNotification
    {
        return $this
            ->createQueryBuilder('notif')
            ->where('notif.userIdentifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
