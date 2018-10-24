<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\AccessControlService;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppRoutingExtension extends RoutingExtension
{

    /**
     * @var AccessControlService
     */
    private $accessControlService;

    public function __construct(AccessControlService $accessControlService, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($urlGenerator);

        $this->accessControlService = $accessControlService;
    }

    public function getPath($name, $parameters = array(), $relative = false)
    {
        if (!$this->accessControlService->checkAccess($name)) {
            return "#noaccess";
        }

        return parent::getPath($name, $parameters, $relative);
    }
}
