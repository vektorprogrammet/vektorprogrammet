<?php

namespace App\Repository\Contract;

use App\Models\UnhandledAccessRule;

/**
 * Interface for UnhandledAccessRule repository operations.
 * This interface defines the contract for unhandled access rule data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface UnhandledAccessRuleRepositoryInterface
{
    /**
     * Find unhandled access rules by resource and method.
     *
     * @param string $resource
     * @param string $method
     * @return UnhandledAccessRule[]
     */
    public function findByResourceAndMethod(string $resource, string $method): array;
}

