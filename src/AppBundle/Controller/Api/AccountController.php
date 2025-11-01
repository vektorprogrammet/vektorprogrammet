<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException;
use BCC\AutoMapperBundle\Mapper\MapperInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AccountController extends BaseController
{
    private $userRepository;
    private $passwordEncoder;
    private $tokenStorage;
    private $session;
    private $mapper;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @param MapperInterface $mapper
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        MapperInterface $mapper
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->mapper = $mapper;
    }

    /**
     * @Route(path="api/account/login", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws InvalidClassConstructorException
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

        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->session->set('_security_secured_area', serialize($token));

        $this->mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $this->mapper->map($user, $userDto);

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
     * @Route(path="api/account/user", methods={"GET"})
     *
     * @return Response
     * @throws InvalidClassConstructorException
     */
    public function getUserAction()
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $this->mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $this->mapper->map($this->getUser(), $userDto);

        return new JsonResponse($userDto);
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
