<?php

namespace App\Repository\Contract;

use App\Models\PasswordReset;
use App\Models\User;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface for PasswordReset repository operations.
 * This interface defines the contract for password reset data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface PasswordResetRepositoryInterface
{
    /**
     * Find password resets by user.
     *
     * @param User $user
     * @return PasswordReset[]
     */
    public function findByUser(User $user): array;

    /**
     * Find user by reset code.
     *
     * @param string $hashedResetCode
     * @return User
     * @throws NonUniqueResultException
     */
    public function findUserByResetcode($hashedResetCode): User;

    /**
     * Find password reset by hashed reset code.
     *
     * @param string $hashedResetCode
     * @return PasswordReset|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws NonUniqueResultException
     */
    public function findPasswordResetByHashedResetCode($hashedResetCode): ?PasswordReset;

    /**
     * Delete password reset by hashed reset code.
     *
     * @param string $hashedResetCode
     * @return mixed
     */
    public function deletePasswordResetByHashedResetCode($hashedResetCode);

    /**
     * Delete password resets by user.
     *
     * @param User $user
     * @return mixed
     */
    public function deletePasswordResetsByUser($user);
}

