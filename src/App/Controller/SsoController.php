<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SsoController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly UserPasswordHasherInterface $passwordHasher,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/sso/login', name: 'sso_login', methods: ['POST'])]
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
            $user = $this->userRepo->findByUsernameOrEmail($username);
        } catch (NoResultException) {
            $response->setStatusCode(401);
            $response->setContent('Username does not exist');

            return $response;
        }

        $validPassword = $this->passwordHasher->isPasswordValid($user, $password);
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
