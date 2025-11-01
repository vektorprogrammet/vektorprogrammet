<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Role;
use AppBundle\Repository\Contract\RoleRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
    /**
     * @param string $roleName
     *
     * @return Role
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findByRoleName(string $roleName): Role
    {
        return $this->createQueryBuilder('role')
            ->select('role')
            ->where('role.role = :roleName')
            ->setParameter('roleName', $roleName)
            ->getQuery()
            ->getSingleResult();
    }
}
