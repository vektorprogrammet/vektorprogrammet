<?php

namespace AppBundle\Service\Contract;

/**
 * Interface for LoginManager service.
 * Defines contract for login management operations.
 */
interface LoginManagerInterface
{
    /**
     * Render login page with message and redirect path.
     *
     * @param string $message
     * @param string $redirectPath
     * @return string
     */
    public function renderLogin(string $message, string $redirectPath);
}
