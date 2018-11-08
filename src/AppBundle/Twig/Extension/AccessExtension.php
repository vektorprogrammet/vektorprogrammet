<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\AccessControlService;

class AccessExtension extends \Twig_Extension
{
    private $accessControlService;

    /**
     * @param AccessControlService $accessControlService
     */
    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('has_access_to', array($this, 'hasAccessTo')),
        );
    }

    /**
     * Checks if the user has access to the resource.
     *
     *
     * @param $routes
     * @param null $user
     * @return boolean True if the user has access to the resource, false otherwise
     */
    public function hasAccessTo($routes, $user = null) : bool
    {
        return $this->accessControlService->checkAccess($routes, $user);
    }
}
