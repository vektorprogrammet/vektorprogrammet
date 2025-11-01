<?php

namespace App\Repository\Contract;

use App\Models\AccessRule;

/**
 * Interface for AccessRule repository operations.
 * This interface defines the contract for access rule data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface AccessRuleRepositoryInterface
{
    /**
     * Find all access rules ordered by resource, method, and name.
     *
     * @return AccessRule[]
     */
    public function findAll(): array;

    /**
     * Find access rules by resource and method.
     *
     * @param string $resource
     * @param string $method
     * @return AccessRule[]
     */
    public function findByResourceAndMethod(string $resource, string $method): array;

    /**
     * Find routing rules.
     *
     * @return AccessRule[]
     */
    public function findRoutingRules(): array;

    /**
     * Find custom rules.
     *
     * @return AccessRule[]
     */
    public function findCustomRules(): array;
}

