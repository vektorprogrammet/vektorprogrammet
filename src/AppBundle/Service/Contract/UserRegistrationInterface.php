<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\User;
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
    public function setNewUserCode(User $user);

    /**
     * Create activation email for user.
     *
     * @param User $user
     * @param string $newUserCode
     * @return Swift_Message
     */
    public function createActivationEmail(User $user, $newUserCode);

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
    public function activateUserByNewUserCode(string $newUserCode);
}
