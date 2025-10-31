<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\PasswordReset;

/**
 * Interface for PasswordManager service.
 * Defines contract for password management operations.
 */
interface PasswordManagerInterface
{
    /**
     * Generate a random reset code.
     *
     * @return string
     */
    public function generateRandomResetCode(): string;

    /**
     * Hash a reset code.
     *
     * @param string $resetCode
     * @return string
     */
    public function hashCode(string $resetCode): string;

    /**
     * Check if reset code is valid.
     *
     * @param string $resetCode
     * @return bool
     */
    public function resetCodeIsValid(string $resetCode): bool;

    /**
     * Check if reset code has expired.
     *
     * @param string $resetCode
     * @return bool
     */
    public function resetCodeHasExpired(string $resetCode): bool;

    /**
     * Get password reset by reset code.
     *
     * @param string $resetCode
     * @return PasswordReset
     */
    public function getPasswordResetByResetCode(string $resetCode): PasswordReset;

    /**
     * Create password reset entity for email.
     *
     * @param string $email
     * @return PasswordReset|null
     */
    public function createPasswordResetEntity(string $email): ?PasswordReset;

    /**
     * Send reset code to user.
     *
     * @param PasswordReset $passwordReset
     */
    public function sendResetCode(PasswordReset $passwordReset);
}
