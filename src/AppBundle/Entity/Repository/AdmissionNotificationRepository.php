<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class AdmissionNotificationRepository extends EntityRepository
{
    /**
     *
     * @param AdmissionSubscriber $subscriber
     * @param Semester $semester
     *
     * @return AdmissionSubscriber
     */
    public function findBySubscriberAndSemester(AdmissionSubscriber $subscriber, Semester $semester)
    {
        return $this->createQueryBuilder('notification')
            ->select('notification')
            ->where('notification.subscriber = :subscriber')
            ->andWhere('notification.semester = :semester')
            ->setParameter('subscriber', $subscriber)
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
