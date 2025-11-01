<?php

namespace App\Repository\Eloquent;

use App\Models\UnhandledAccessRule;
use App\Repository\Contract\UnhandledAccessRuleRepositoryInterface;

/**
 * Eloquent implementation of UnhandledAccessRuleRepositoryInterface.
 */
class UnhandledAccessRuleRepository implements UnhandledAccessRuleRepositoryInterface
{
    /**
     * Find unhandled access rules by resource and method.
     *
     * @param string $resource
     * @param string $method
     * @return UnhandledAccessRule[]
     */
    public function findByResourceAndMethod(string $resource, string $method): array
    {
        return UnhandledAccessRule::where('resource', $resource)
            ->where('method', $method)
            ->get()
            ->toArray();
    }
}

