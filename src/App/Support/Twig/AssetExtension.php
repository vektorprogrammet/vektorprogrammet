<?php

namespace App\Support\Twig;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    /**
     * AssetExtension constructor.
     */
    public function __construct(private readonly Packages $packages, private readonly string $rootDir)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('asset_with_version', $this->getAssetUrl(...)),
        ];
    }

    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     *
     * @param string $path        A public path
     * @param string $packageName The name of the asset package to use
     *
     * @return string The public path of the asset
     */
    public function getAssetUrl($path, $packageName = null)
    {
        if ($path === null || strlen((string) $path) === 0) {
            return '';
        }

        if ($path[0] !== '/') {
            $path = "/$path";
        }
        $filePath = $this->rootDir."/web$path";

        $version = '';
        if (file_exists($filePath)) {
            $version = filemtime($filePath);
        }

        $url = $this->packages->getUrl($path, $packageName);
        if (strlen($version) > 0) {
            $url .= '?v='.$version;
        }

        return $url;
    }
}
