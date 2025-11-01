<?php

namespace App\Contract;

use App\Models\User;
use Swift_Message;

/**
 * Interface for UserRegistration service.
 * Defines contract for user registration operations.
 */
interface UserRegistrationInterface
{
    /**
     * Set new user code for user.
     *
     * @param User $user
     * @return string
     */
    public function setNewUserCode(User $user): string;

    /**
     * Create activation email for user.
     *
     * @param User $user
     * @param string $newUserCode
     * @return Swift_Message
     */
    public function createActivationEmail(User $user, string $newUserCode): Swift_Message;

    /**
     * Send activation code to user.
     *
     * @param User $user
     */
    public function sendActivationCode(User $user);

    /**
     * Get hashed code from new user code.
     *
     * @param string $newUserCode
     * @return string
     */
    public function getHashedCode(string $newUserCode): string;

    /**
     * Activate user by new user code.
     *
     * @param string $newUserCode
     * @return User|null
     */
    public function activateUserByNewUserCode(string $newUserCode): ?User;
}
