<?php

namespace App\Service;

use App\Entity\User;
use App\Role\Roles;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BetaRedirecter
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RoleManager $roleManager,
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return $event;
        }
        if (!$this->userShouldBeRedirected()) {
            return $event;
        }

        $request = $event->getRequest();
        $host = $request->headers->get('host');
        $isLiveServer = str_contains((string) $host, 'vektorprogrammet.no');
        $isBeta = str_contains((string) $host, 'beta');

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
