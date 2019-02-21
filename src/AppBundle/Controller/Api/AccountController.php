<?php

namespace AppBundle\Controller\Api;

use AppBundle\DataTransferObject\AssistantHistoryDto;
use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\User;
use BCC\AutoMapperBundle\Mapper\FieldFilter\ObjectMappingFilter;
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
        $userDto = new UserDto();
        $mapper->map($user, $userDto);

        return new JsonResponse($userDto);
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
     * @Route("api/account/mypartner", name="my_partner")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMyPartnerAction()
    {
        if (!$this->getUser()->isActive()) {
            throw $this->createAccessDeniedException();
        }
        $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($this->getUser());
        if (empty($activeAssistantHistories)) {
            throw $this->createNotFoundException();
        }

        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
            $partners = [];

            foreach ($schoolHistories as $sh) {
                if ($sh->getUser() === $this->getUser()) {
                    continue;
                }
                if ($sh->getDay() !== $activeHistory->getDay()) {
                    continue;
                }
                if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
                    $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                    $partners[] = $sh;
                    $partnerCount++;
                }
            }
            $partnerInformations[] = [
                'school' => $activeHistory->getSchool(),
                'assistantHistory' => $activeHistory,
                'partners' => $partners,
            ];
        }

        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
        $info = [$partnerInformations, $partnerCount, $semester];
        $mapper = $this->get('bcc_auto_mapper.mapper');
        $assistantHistoryDto = new AssistantHistoryDto();
        $mapper->map($activeAssistantHistories[0], $assistantHistoryDto);
        return new JsonResponse($assistantHistoryDto);
    }

}
