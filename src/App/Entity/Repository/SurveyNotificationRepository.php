<?php

namespace App\Entity\Repository;

use App\Entity\SurveyNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class SurveyNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyNotification::class);
    }

    /**
     * @return SurveyNotification?
     *
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
