<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;

class RouteDisplayExtension extends \Twig_Extension
{
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_path', array( $this, 'getPath' )),
        );
    }

    /**
     * Gets the path of the given route name.
     *
     *
     * @param $name
     *
     * @return string The path of the route
     */
    public function getPath(string $name)
    {
        if (!$this->isRoute($name)) {
            return $name;
        }

        return $this->router->getRouteCollection()->get($name)->getPath();
    }

    private function isRoute(string $name)
    {
        return $this->router->getRouteCollection()->get($name) !== null;
    }
}
