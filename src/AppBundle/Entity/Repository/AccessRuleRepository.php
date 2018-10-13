<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * AccessRuleRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class AccessRuleRepository extends EntityRepository
{
    public function findAll()
    {
        return $this
            ->createQueryBuilder('rule')
            ->orderBy('rule.resource')
            ->addOrderBy('rule.method')
            ->addOrderBy('rule.name')
            ->getQuery()
            ->getResult();
    }

    public function findRoutingRules()
    {
        return $this->findRules(true);
    }

    public function findCustomRules()
    {
        return $this->findRules(false);
    }

    private function findRules(bool $isRouting)
    {
        return $this
            ->createQueryBuilder('rule')
            ->where('rule.isRoutingRule = :isRouting')
            ->setParameter('isRouting', $isRouting)
            ->orderBy('rule.resource')
            ->addOrderBy('rule.method')
            ->addOrderBy('rule.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $resource
     * @param string $method
     *
     * @return AccessRule[]
     */
    public function findOneByResourceAndMethod(string $resource, string $method)
    {
        return $this
            ->findByResourceAndMethodQuery($resource, $method)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @param AccessRule $accessRule
     *
     * @return bool
     */
    public function usersTeamIsInAccessRule(User $user, AccessRule $accessRule)
    {
        $count = $this
            ->countByAccessRule($accessRule)
            ->join('accessRule.teams', 'teams')
            ->join('teams.teamMemberships', "membership")
            ->andWhere('membership.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        if ($accessRule->isForExecutiveBoard()) {
            $count += count($user->getActiveExecutiveBoardMemberships());
        }

        return intval($count) > 0;
    }

    /**
     * @param User $user
     *
     * @param AccessRule $accessRule
     *
     * @return bool
     */
    public function userIsInAccessRule(User $user, AccessRule $accessRule)
    {
        $count = $this
            ->countByAccessRule($accessRule)
            ->join('accessRule.users', 'users')
            ->andWhere('users = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return intval($count) > 0;
    }

    /**
     * @param Role $role
     * @param AccessRule $accessRule
     *
     * @return bool
     */
    public function roleIsInAccessRule(Role $role, AccessRule $accessRule)
    {
        $count = $this
            ->countByAccessRule($accessRule)
            ->join('accessRule.roles', 'roles')
            ->andWhere('roles = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getSingleScalarResult();

        return intval($count) > 0;
    }

    private function findByResourceAndMethodQuery(string $resource, string $method)
    {
        return $this
            ->createQueryBuilder('accessRule')
            ->andWhere('accessRule.resource = :resource')
            ->andWhere('accessRule.method = :method')
            ->setParameter('resource', $resource)
            ->setParameter('method', $method);
    }

    private function countByAccessRule(AccessRule $accessRule)
    {
        return $this
            ->createQueryBuilder('accessRule')
            ->select('COUNT(accessRule.id)')
            ->andWhere('accessRule = :accessRule')
            ->setParameter('accessRule', $accessRule);
    }
}
