<?php

namespace App\Repository\Eloquent;

use App\Models\Signature;
use App\Models\User;
use App\Repository\Contract\SignatureRepositoryInterface;

/**
 * Eloquent implementation of SignatureRepositoryInterface.
 */
class SignatureRepository implements SignatureRepositoryInterface
{
    /**
     * Find signature by user.
     *
     * @param User $user
     * @return Signature|null
     */
    public function findByUser(User $user): ?Signature
    {
        return Signature::where('user_id', $user->id)->first();
    }
}

