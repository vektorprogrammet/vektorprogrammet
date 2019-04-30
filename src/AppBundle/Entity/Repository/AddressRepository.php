<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class AddressRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Address
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAddressByUser(User $user):? Address
    {
        try {
            return $this->createQueryBuilder('address')
                ->select('address')
                ->where('address.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }

    }


}
