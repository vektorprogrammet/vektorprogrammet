<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Service\AccessControlService;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppRoutingExtension extends AbstractExtension
{
    private RoutingExtension $routing;

    /**
     * @var AccessControlService
     */
    private $accessControlService;

    public function __construct(AccessControlService $accessControlService, UrlGeneratorInterface $urlGenerator)
    {
        $this->routing = new RoutingExtension($urlGenerator);

        $this->accessControlService = $accessControlService;
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('path', [$this, 'getPath'], ['is_safe_callback' => [$this->routing, 'isUrlGenerationSafe']])];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        if (!$this->accessControlService->checkAccess($name)) {
            return "#noaccess";
        }

        return $this->routing->getPath($name, $parameters, $relative);
    }
}
