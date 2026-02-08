<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\DataTransferObject\UserDto;
use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Exception;

class AccountController extends BaseController
{

    /**
     * @Route(path="api/account/login", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws NonUniqueResultException
     */
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
            $user = $this->getDoctrine()->getRepository(User::class)->findByUsernameOrEmail($username);
        } catch (NoResultException $e) {
            $response->setStatusCode(401);
            $response->setContent('Username does not exist');
            return $response;
        }

        $validPassword = $this->get('security.password_encoder')->isPasswordValid($user, $password);
        if (!$validPassword) {
            $response->setStatusCode(401);
            $response->setContent('Wrong password');
            return $response;
        }

        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_secured_area', serialize($token));

        $userDto = self::mapUserToDto($user);

        return new JsonResponse($userDto);
    }

    /**
     * @Route(path="api/account/logout", methods={"POST"})
     *
     * @return Response
     */
    public function logoutAction()
    {
        try {
            $this->get('security.token_storage')->setToken(null);
            return new JsonResponse("Logout successful");
        } catch (Exception $e) {
            $response = new JsonResponse();
            $response->setStatusCode(401);
            $response->setContent($e);
            return $response;
        }
    }

    /**
     * @Route(path="api/account/user", methods={"GET"})
     *
     * @return Response
     */
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
     * @Route(
     *     path="api/account/get_department",
     *     methods={"GET"}
     * )
     *
     * @return Response
     */
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
