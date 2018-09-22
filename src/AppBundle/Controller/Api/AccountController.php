<?php

namespace AppBundle\Controller\Api;

use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AccountController extends Controller {

	/**
	 * @Route("api/account/login")
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request) {
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
	 * @Route("api/account/login")
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getUserAction(Request $request) {
		dump($_POST);
		$username = $request->request->get('username');
		$password = $request->request->get('password');
		return new JsonResponse([$username, $password]);
	}
}
