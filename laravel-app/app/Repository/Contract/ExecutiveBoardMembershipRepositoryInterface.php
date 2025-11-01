<?php

namespace App\Repository\Contract;

use App\Models\ExecutiveBoardMembership;
use App\Models\User;

/**
 * Interface for ExecutiveBoardMembership repository operations.
 * This interface defines the contract for executive board membership data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ExecutiveBoardMembershipRepositoryInterface
{
    /**
     * Find all executive board memberships.
     *
     * @return ExecutiveBoardMembership[]
     */
    public function findAll(): array;

    /**
     * Find executive board memberships by user.
     *
     * @param User $user
     * @return ExecutiveBoardMembership[]
     */
    public function findByUser(User $user): array;
}

