<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Role\Roles;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class RoleExtension extends \Twig_Extension
{
    private $authorizationChecker;
    private $tokenStorage;

    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
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
        );
    }

    public function isGrantedAssistant()
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted(Roles::ASSISTANT);
    }

    public function isGrantedTeamMember()
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted(Roles::TEAM_MEMBER);
    }

    public function isGrantedTeamLeader()
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted(Roles::TEAM_LEADER);
    }

    public function isGrantedAdmin()
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted(Roles::ADMIN);
    }
}
