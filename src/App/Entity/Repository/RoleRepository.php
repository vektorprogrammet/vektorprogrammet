<?php

namespace App\Entity\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

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
