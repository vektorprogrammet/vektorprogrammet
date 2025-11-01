<?php

namespace App\Contract;

use App\Models\AccessRule;
use App\Models\User;

/**
 * Interface for AccessControlService service.
 * Defines contract for access control operations.
 */
interface AccessControlServiceInterface
{
    /**
     * Create access rule.
     *
     * @param AccessRule $accessRule
     */
    public function createRule(AccessRule $accessRule);

    /**
     * Check access for resources.
     *
     * @param string|array $resources
     * @param User|null $user
     * @return bool
     */
    public function checkAccess($resources, User $user = null): bool;

    /**
     * Get all routes.
     *
     * @return array
     */
    public function getRoutes(): array;

    /**
     * Get path for route name.
     *
     * @param string $name
     * @return string
     */
    public function getPath(string $name): string;
}
