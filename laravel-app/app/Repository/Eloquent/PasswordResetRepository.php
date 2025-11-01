<?php

namespace App\Repository\Eloquent;

use App\Models\PasswordReset;
use App\Models\User;
use App\Repository\Contract\PasswordResetRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of PasswordResetRepositoryInterface.
 */
class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    /**
     * Find password resets by user.
     *
     * @param User $user
     * @return PasswordReset[]
     */
    public function findByUser(User $user): array
    {
        return PasswordReset::where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Find user by reset code.
     *
     * @param string $hashedResetCode
     * @return User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByResetcode($hashedResetCode): User
    {
        $passwordReset = PasswordReset::where('hashed_reset_code', $hashedResetCode)
            ->with('user')
            ->first();

        if (!$passwordReset || !$passwordReset->user) {
            throw new ModelNotFoundException('Password reset not found for the given code.');
        }

        return $passwordReset->user;
    }

    /**
     * Find password reset by hashed reset code.
     *
     * @param string $hashedResetCode
     * @return PasswordReset|null
     */
    public function findPasswordResetByHashedResetCode($hashedResetCode): ?PasswordReset
    {
        return PasswordReset::where('hashed_reset_code', $hashedResetCode)->first();
    }

    /**
     * Delete password reset by hashed reset code.
     *
     * @param string $hashedResetCode
     * @return mixed
     */
    public function deletePasswordResetByHashedResetCode($hashedResetCode)
    {
        return PasswordReset::where('hashed_reset_code', $hashedResetCode)->delete();
    }

    /**
     * Delete password resets by user.
     *
     * @param User $user
     * @return mixed
     */
    public function deletePasswordResetsByUser($user)
    {
        return PasswordReset::where('user_id', $user->id)->delete();
    }
}

