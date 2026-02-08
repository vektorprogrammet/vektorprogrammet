<?php

namespace App\Entity\Repository;

use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RoleRepository extends EntityRepository
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
