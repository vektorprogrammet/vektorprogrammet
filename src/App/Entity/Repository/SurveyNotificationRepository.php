<?php

namespace App\Entity\Repository;

use App\Entity\SurveyNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class SurveyNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyNotification::class);
    }


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
