<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\DataTransferObject\UserDto;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Exception;

class AccountController extends BaseController
{
    public function __construct(
        private UserRepository $userRepo,
        private UserPasswordHasherInterface $passwordHasher,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route(path: "api/account/login", methods: ["GET", "POST"])]
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
        } catch (NoResultException $e) {
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

        $token = new UsernamePasswordToken($user, 'secured_area', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_secured_area', serialize($token));

        $userDto = self::mapUserToDto($user);

        return new JsonResponse($userDto);
    }

    /**
     * @return Response
     */
    #[Route(path: "api/account/logout", methods: ["POST"])]
    public function logoutAction()
    {
        try {
            $this->tokenStorage->setToken(null);
            return new JsonResponse("Logout successful");
        } catch (Exception $e) {
            $response = new JsonResponse();
            $response->setStatusCode(401);
            $response->setContent($e);
            return $response;
        }
    }

    /**
     * @return Response
     */
    #[Route(path: "api/account/user", methods: ["GET"])]
    public function getUserAction()
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $userDto = self::mapUserToDto($this->getUser());

        return new JsonResponse($userDto);
    }

    private static function mapUserToDto(User $user): UserDto
    {
        $dto = new UserDto();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->fullName = $user->getFullName();
        $dto->username = $user->getUserName();
        $dto->email = $user->getEmail();
        $dto->companyEmail = $user->getCompanyEmail();
        $dto->isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        return $dto;
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route(path: "api/account/get_department", methods: ["GET"])]
    public function getDepartmentApi(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $department = $this->getUser()->getDepartment();

        if (!$department) {
            return new JsonResponse(null);
        }

        // This is not a proper DTO, and should be changed, but as we really only need the id for now... :
        $departmentDto = array(
            "id" => $department->getId(),
            "name" => $department->getName(),
        );

        return new JsonResponse($departmentDto);
    }
}
