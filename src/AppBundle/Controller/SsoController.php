<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SsoController extends BaseController
{
    private $userRepository;
    private $passwordEncoder;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }
    public function loginAction(Request $request)
    {
        $response = new JsonResponse();

        $username = $request->get('username');
        $password = $request->get('password');

        if (!$username || !$password) {
            $response->setStatusCode(401);
            $response->setContent('Username or password not provided');
            return $response;
        }

        try {
            $user = $this->userRepository->findByUsernameOrEmail($username);
        } catch (NoResultException $e) {
            $response->setStatusCode(401);
            $response->setContent('Username does not exist');
            return $response;
        }

        $validPassword = $this->passwordEncoder->isPasswordValid($user, $password);
        if (!$validPassword) {
            $response->setStatusCode(401);
            $response->setContent('Wrong password');
            return $response;
        }

        $activeInTeam = count($user->getActiveMemberships()) > 0;
        if (!$activeInTeam) {
            $response->setStatusCode(401);
            $response->setContent('User does not have any active team memberships');
            return $response;
        }

        return new JsonResponse([
            'name' => $user->getFullName(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'companyEmail' => $user->getCompanyEmail(),
        ]);
    }
}
