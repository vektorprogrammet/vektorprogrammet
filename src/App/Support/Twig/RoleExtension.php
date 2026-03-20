<?php

namespace App\Support\Twig;

use App\Identity\Infrastructure\Entity\User;
use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoleExtension extends AbstractExtension
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker, private readonly TokenStorageInterface $tokenStorage, private readonly RoleManager $roleManager)
    {
    }

    public function getName()
    {
        return 'role_extension';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_granted_assistant', $this->isGrantedAssistant(...)),
            new TwigFunction('is_granted_team_member', $this->isGrantedTeamMember(...)),
            new TwigFunction('is_granted_team_leader', $this->isGrantedTeamLeader(...)),
            new TwigFunction('is_granted_admin', $this->isGrantedAdmin(...)),
            new TwigFunction('can_edit_page', $this->userCanEditPage(...)),
            new TwigFunction('user_is_granted_assistant', $this->userIsGrantedAssistant(...)),
            new TwigFunction('user_is_granted_team_member', $this->userIsGrantedTeamMember(...)),
            new TwigFunction('user_is_granted_team_leader', $this->userIsGrantedTeamLeader(...)),
            new TwigFunction('user_is_granted_admin', $this->userIsGrantedAdmin(...)),
            new TwigFunction('user_is_in_executive_board', $this->userIsInExecutiveBoard(...)),
        ];
    }

    public function isGrantedAssistant()
    {
        return $this->isGranted(Roles::ASSISTANT);
    }

    public function isGrantedTeamMember()
    {
        return $this->isGranted(Roles::TEAM_MEMBER);
    }

    public function isGrantedTeamLeader()
    {
        return $this->isGranted(Roles::TEAM_LEADER);
    }

    public function isGrantedAdmin()
    {
        return $this->isGranted(Roles::ADMIN);
    }

    private function isGranted(string $role): bool
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted($role);
    }

    public function userIsGrantedAssistant(User $user)
    {
        return $this->roleManager->userIsGranted($user, Roles::ASSISTANT);
    }

    public function userIsGrantedTeamMember(User $user)
    {
        return $this->roleManager->userIsGranted($user, Roles::TEAM_MEMBER);
    }

    public function userIsGrantedTeamLeader(User $user)
    {
        return $this->roleManager->userIsGranted($user, Roles::TEAM_LEADER);
    }

    public function userIsGrantedAdmin(User $user)
    {
        return $this->roleManager->userIsGranted($user, Roles::ADMIN);
    }

    public function userIsInExecutiveBoard(User $user)
    {
        return $this->roleManager->userIsInExecutiveBoard($user);
    }

    public function userCanEditPage(?User $user = null)
    {
        if ($user === null) {
            $token = $this->tokenStorage->getToken();
            if (!$token) {
                return false;
            }
            $user = $token->getUser();
        }

        if (!$user || !is_object($user) || $user::class !== User::class) {
            return false;
        }

        return $this->roleManager->userIsGranted($user, Roles::ADMIN) || $this->roleManager->userIsInExecutiveBoard($user);
    }
}
