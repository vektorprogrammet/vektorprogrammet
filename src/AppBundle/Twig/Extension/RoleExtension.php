<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use AppBundle\Service\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class RoleExtension extends \Twig_Extension
{
    private $authorizationChecker;
    private $tokenStorage;
    /**
     * @var RoleManager
     */
    private $roleManager;

    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, RoleManager $roleManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->roleManager = $roleManager;
    }

    public function getName()
    {
        return 'role_extension';
    }

    public function getFunctions()
    {
        return array(
            'is_granted_assistant' => new \Twig_Function_Method($this, 'isGrantedAssistant'),
            'is_granted_team_member' => new \Twig_Function_Method($this, 'isGrantedTeamMember'),
            'is_granted_team_leader' => new \Twig_Function_Method($this, 'isGrantedTeamLeader'),
            'is_granted_admin' => new \Twig_Function_Method($this, 'isGrantedAdmin'),
            'can_edit_page' => new \Twig_Function_Method($this, 'userCanEditPage'),
            'user_is_granted_assistant' => new \Twig_Function_Method($this, 'userIsGrantedAssistant'),
            'user_is_granted_team_member' => new \Twig_Function_Method($this, 'userIsGrantedTeamMember'),
            'user_is_granted_team_leader' => new \Twig_Function_Method($this, 'userIsGrantedTeamLeader'),
            'user_is_granted_admin' => new \Twig_Function_Method($this, 'userIsGrantedAdmin'),
            'user_is_in_executive_board' => new \Twig_Function_Method($this, 'userIsInExecutiveBoard'),
        );
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

    private function isGranted(string $role) : bool
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

    public function userCanEditPage(User $user = null)
    {
        if ($user === null) {
            $token = $this->tokenStorage->getToken();
            if (!$token) {
                return false;
            }
            $user = $token->getUser();
        }

        if (!$user || !is_object($user) || get_class($user) !== User::class) {
            return false;
        }

        return $this->roleManager->userIsGranted($user, Roles::ADMIN) || $this->roleManager->userIsInExecutiveBoard($user);
    }
}
