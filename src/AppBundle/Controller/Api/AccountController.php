<?php

namespace AppBundle\Controller\Api;

use AppBundle\DataTransferObject\UserDto;
use AppBundle\DataTransferObject\DepartmentDto;
use AppBundle\Entity\User;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AccountController extends BaseController
{

    /**
     * @Route("api/account/login")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findByUsernameOrEmail($username);
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

        $mapper = $this->get('bcc_auto_mapper.mapper');
        $mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $mapper->map($user, $userDto);

        return new JsonResponse($userDto);
    }

    /**
     * @Route("api/account/logout")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logoutAction(Request $request)
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
     * @Route("api/account/user")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException
     */
    public function getUserAction()
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $mapper = $this->get('bcc_auto_mapper.mapper');
        $mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $mapper->map($this->getUser(), $userDto);

        return new JsonResponse($userDto);
    }


    /**
     * @param Request $request
     *
     * @Route(
     *     "api/account/get_department",
     *     methods={"GET"}
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
