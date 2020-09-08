<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginManager
{
    private $twig;
    private $authenticationUtils;
    private $router;

    /**
     * LoginManager constructor.
     *
     * @param $twig
     * @param $authenticationUtils
     * @param $router
     */
    public function __construct(Environment $twig, AuthenticationUtils $authenticationUtils, Router $router)
    {
        $this->twig = $twig;
        $this->authenticationUtils = $authenticationUtils;
        $this->router = $router;
    }

    public function renderLogin(string $message, string $redirectPath)
    {
        return $this->twig->render('login/login.html.twig', array(
            'last_username' => null,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'message' => $message,
            'redirect_path' => $this->router->generate($redirectPath),
        ));
    }
}
