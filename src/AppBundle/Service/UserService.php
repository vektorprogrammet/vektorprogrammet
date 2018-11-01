<?php


namespace AppBundle\Service;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return null|User
     */
    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }
        $user = $token->getUser();
        if (!$user || !$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function getCurrentUserName() : string
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return "Anonymous";
        }

        return $user->__toString();
    }

    public function getCurrentUserNameAndDepartment() : string
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return "Anonymous";
        }
        $department = $user->getDepartment();

        return $user->__toString() . " ($department)";
    }

    public function getCurrentProfilePicture() : string
    {
        $user = $this->getCurrentUser();

        return "https://vektorprogrammet.no/" . ($user ? $user->getPicturePath() : "images/defaultProfile.png");
    }
}
