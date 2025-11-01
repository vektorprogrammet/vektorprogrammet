<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\User;

/**
 * Interface for ExecutiveBoardMembership repository operations.
 * This interface defines the contract for executive board membership data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ExecutiveBoardMembershipRepositoryInterface
{
    /**
     * Find executive board memberships by user.
     *
     * @param User $user
     * @return ExecutiveBoardMembership[]
     */
    public function findByUser(User $user): array;
}

