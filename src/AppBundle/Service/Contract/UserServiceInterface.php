<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\User;

/**
 * Interface for UserService service.
 * Defines contract for user service operations.
 */
interface UserServiceInterface
{
    /**
     * Get the current logged-in user.
     *
     * @return null|User
     */
    public function getCurrentUser();

    /**
     * Get the current user's name.
     *
     * @return string
     */
    public function getCurrentUserName(): string;

    /**
     * Get the current user's name and department.
     *
     * @return string
     */
    public function getCurrentUserNameAndDepartment(): string;

    /**
     * Get the current user's profile picture URL.
     *
     * @return string
     */
    public function getCurrentProfilePicture(): string;
}
