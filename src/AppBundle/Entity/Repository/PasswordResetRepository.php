<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\PasswordReset;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * PasswordResetRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PasswordResetRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('password_reset')
            ->select('password_reset')
            ->where('password_reset.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
    public function findUserByResetcode($hashedResetCode)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->select('PasswordReset.user')
            ->where('PasswordReset.hashedResetCode = :hashedResetCode')
            ->setParameter('hashedResetCode', $hashedResetCode)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param $hashedResetCode
     *
     * @return PasswordReset
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findPasswordResetByHashedResetCode($hashedResetCode)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->select('PasswordReset')
            ->where('PasswordReset.hashedResetCode = :hashedResetCode')
            ->setParameter('hashedResetCode', $hashedResetCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deletePasswordResetByHashedResetCode($hashedResetCode)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->delete()
            ->where('PasswordReset.hashedResetCode = :hashedResetCode')
            ->setParameter('hashedResetCode', $hashedResetCode)
            ->getQuery()
            ->getResult();
    }

    public function deletePasswordResetsByUser($user)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->delete()
            ->where('PasswordReset.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
