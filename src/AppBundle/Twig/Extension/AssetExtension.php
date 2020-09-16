<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private $packages;
    private $rootDir;

    /**
     * AssetExtension constructor.
     *
     * @param Packages $packages
     * @param string   $rootDir
     */
    public function __construct(Packages $packages, string $rootDir)
    {
        $this->packages = $packages;
        $this->rootDir = $rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('asset_with_version', array($this, 'getAssetUrl')),
        );
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
        if (strlen($path) === 0) {
            return $path;
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
