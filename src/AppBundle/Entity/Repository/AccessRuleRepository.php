<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Role;
use AppBundle\Entity\TeamMembership;
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

    public function findByResourceAndMethod(string $resource, string $method)
    {
        return $this
            ->createQueryBuilder('accessRule')
            ->andWhere('accessRule.resource = :resource')
            ->setParameter('resource', $resource)
            ->andWhere('accessRule.method = :method')
            ->setParameter('method', $method)
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
}