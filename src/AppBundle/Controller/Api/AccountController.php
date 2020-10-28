<?php

namespace AppBundle\Controller\Api;

use AppBundle\DataTransferObject\AssistantHistoryDto;
use AppBundle\DataTransferObject\DepartmentDto;
use AppBundle\Controller\BaseController;
use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use BCC\AutoMapperBundle\Mapper\FieldFilter\ObjectMappingFilter;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $user = $this->getUser();
        $mapper = $this->get('bcc_auto_mapper.mapper');
        $mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $mapper->map($user, $userDto);


        $department = $user->getDepartment();
        $mapper->createMap(Department::class, DepartmentDto::class);
        $departmentDto = new DepartmentDto();
        $mapper->map($department, $departmentDto);
        $userDto->department = $departmentDto;
        return new JsonResponse($userDto);
    }

    /**
     * @Route("api/account/my-partner", name="my_partner")
     *
     * @return JsonResponse
     * @throws \BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException
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

        $partnerHistories = [];
        $reserveHistory = [];
        if (count($activeAssistantHistories) > 0) {
            $reserveHistory = $activeAssistantHistories[0];
        }

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());

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
                    $partnerHistories[] = $sh;
                }
            }
        }

        $mapper = $this->get('bcc_auto_mapper.mapper');
        $partnerHistoryDtos = [];
        if (count($partnerHistories) > 0) {
            foreach ($partnerHistories as $ph) {
                $assistantHistoryDto = new AssistantHistoryDto();
                $mapper->map($ph, $assistantHistoryDto);
                $partnerHistoryDtos[] = $assistantHistoryDto;
            }
        }

        return new JsonResponse($partnerHistoryDtos);
    }

    /**
     * @Route("api/account/my-schedule", name="my_schedule")
     *
     * @return JsonResponse
     * @throws \BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException
     */
    public function getMyScheduleAction()
    {
        if (!$this->getUser()->isActive()) {
            throw $this->createAccessDeniedException();
        }
        $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($this->getUser());

        $mapper = $this->get('bcc_auto_mapper.mapper');

        $activeHistoryDtos = [];
        if (count($activeAssistantHistories) > 0) {
            foreach ($activeAssistantHistories as $ah) {
                $activeHistoryDto = new AssistantHistoryDto();
                $mapper->map($ah, $activeHistoryDto);
                $activeHistoryDtos[] = $activeHistoryDto;
            }
        }
        return new JsonResponse($activeHistoryDtos);
    }

}
