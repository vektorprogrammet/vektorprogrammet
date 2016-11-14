<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Subscriber;
use Doctrine\ORM\EntityRepository;

class SubscriberRepository extends EntityRepository
{
    /**
     * @param string $email
     * @param Newsletter $newsletter
     * @return Subscriber[]
     */
    public function findByEmailAndNewsletter(string $email, Newsletter $newsletter) {
        return $this->createQueryBuilder('subscriber')
            ->select('subscriber')
            ->where('subscriber.email = :email')
            ->andWhere('subscriber.newsletter = :newsletter')
            ->setParameter('email', $email)
            ->setParameter('newsletter', $newsletter)
            ->getQuery()
            ->getResult();
    }
}
