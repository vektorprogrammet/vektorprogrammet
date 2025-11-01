<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\Signature;
use AppBundle\Entity\User;

/**
 * Interface for Signature repository operations.
 * This interface defines the contract for signature data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface SignatureRepositoryInterface
{
    /**
     * Find signature by user.
     *
     * @param User $user
     * @return Signature|null
     */
    public function findByUser(User $user): ?Signature;
}

