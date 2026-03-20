<?php

namespace App\Support\Twig;

use App\Identity\Infrastructure\AccessControlService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppRoutingExtension extends AbstractExtension
{
    public function __construct(private readonly AccessControlService $accessControlService, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', $this->getPath(...)),
            new TwigFunction('url', $this->getUrl(...)),
        ];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        if (!$this->accessControlService->checkAccess($name)) {
            return '#noaccess';
        }

        return $this->urlGenerator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getUrl(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        return $this->urlGenerator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
