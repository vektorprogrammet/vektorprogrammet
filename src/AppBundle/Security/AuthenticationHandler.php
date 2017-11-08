<?php

namespace AppBundle\Security;


use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception )
    {
        return new JsonResponse(["message" => $exception->getMessage()], 401);
    }

    public function onAuthenticationSuccess( Request $request, TokenInterface $token )
    {
        $jsonUser = $this->serializer->serialize($token->getUser(), 'json');
        return new JsonResponse(["user" => json_decode($jsonUser)]);
    }

	public function onLogoutSuccess( Request $request ) {
		return new JsonResponse("Logged out.");
	}
}
