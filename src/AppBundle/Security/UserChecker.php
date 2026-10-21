<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && !$user->isEnabled()) {
            $exception = new DisabledException('User account is disabled.');
            $exception->setUser($user);
            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
