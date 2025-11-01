<?php

namespace App\Repository\Eloquent;

use App\Models\AccessRule;
use App\Repository\Contract\AccessRuleRepositoryInterface;

/**
 * Eloquent implementation of AccessRuleRepositoryInterface.
 */
class AccessRuleRepository implements AccessRuleRepositoryInterface
{
    /**
     * Find all access rules ordered by resource, method, and name.
     *
     * @return AccessRule[]
     */
    public function findAll(): array
    {
        return AccessRule::orderBy('resource')
            ->orderBy('method')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Find access rules by resource and method.
     *
     * @param string $resource
     * @param string $method
     * @return AccessRule[]
     */
    public function findByResourceAndMethod(string $resource, string $method): array
    {
        return AccessRule::where('resource', $resource)
            ->where('method', $method)
            ->get()
            ->toArray();
    }

    /**
     * Find routing rules.
     *
     * @return AccessRule[]
     */
    public function findRoutingRules(): array
    {
        return $this->findRules(true);
    }

    /**
     * Find custom rules.
     *
     * @return AccessRule[]
     */
    public function findCustomRules(): array
    {
        return $this->findRules(false);
    }

    /**
     * Private helper method to find rules by routing flag.
     *
     * @param bool $isRouting
     * @return AccessRule[]
     */
    private function findRules(bool $isRouting): array
    {
        return AccessRule::where('is_routing_rule', $isRouting)
            ->orderBy('resource')
            ->orderBy('method')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}

