<?php

namespace App\Service;

use App\Models\User;
use App\Role\Roles;
use App\Service\Contract\BetaRedirecterInterface;
use App\Service\Contract\RoleManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BetaRedirecter implements BetaRedirecterInterface
{
    private $tokenStorage;
    private $roleManager;

    public function __construct(TokenStorageInterface $tokenStorage, RoleManagerInterface $roleManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->roleManager = $roleManager;
    }

    public function onKernelRequest(GetResponseEvent $event): GetResponseEvent
    {
        if (!$event->isMasterRequest()) {
            return $event;
        }
        if (!$this->userShouldBeRedirected()) {
            return $event;
        }

        $request = $event->getRequest();
        $host = $request->headers->get('host');
        $isLiveServer = strpos($host, 'vektorprogrammet.no') !== false;
        $isBeta = strpos($host, 'beta') !== false;

        if (!$isLiveServer || $isBeta) {
            return $event;
        }

        $betaUrl = str_replace("//$host", "//beta.$host", $request->getUri());
        $event->setResponse(new RedirectResponse($betaUrl));

        return $event;
    }

    private function userShouldBeRedirected()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return false;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->roleManager->userIsGranted($user, Roles::TEAM_LEADER);
    }
}
