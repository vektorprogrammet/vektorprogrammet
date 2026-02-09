<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Semester;
use App\Google\GoogleAPI;
use App\Service\AccessControlService;
use App\Service\AdmissionNotifier;
use App\Service\AdmissionStatistics;
use App\Service\ApplicationAdmission;
use App\Service\ApplicationData;
use App\Service\ApplicationManager;
use App\Service\AssistantHistoryData;
use App\Service\ContentModeManager;
use App\Service\FileUploader;
use App\Service\FilterService;
use App\Service\GeoLocation;
use App\Service\InterviewCounter;
use App\Service\InterviewManager;
use App\Service\LogService;
use App\Service\PasswordManager;
use App\Service\RoleManager;
use App\Service\SbsData;
use App\Service\SlackMessenger;
use App\Service\SlugMaker;
use App\Service\Sorter;
use App\Service\SurveyManager;
use App\Service\SurveyNotifier;
use App\Service\UserGroupCollectionManager;
use App\Service\UserRegistration;
use App\Twig\RoleExtension;
use App\Utils\ReversedRoleHierarchy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'event_dispatcher' => '?Symfony\Component\EventDispatcher\EventDispatcherInterface',
            'security.password_encoder' => '?Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface',
            'kernel' => '?Symfony\Component\HttpKernel\KernelInterface',
            'knp_paginator' => '?Knp\Component\Pager\PaginatorInterface',
            'security.token_storage' => '?Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface',
            'session' => '?Symfony\Component\HttpFoundation\Session\SessionInterface',
            'form.factory' => '?Symfony\Component\Form\FormFactoryInterface',
            'security.authentication_utils' => '?Symfony\Component\Security\Http\Authentication\AuthenticationUtils',
            'security.authorization_checker' => '?Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface',
            'request_stack' => '?Symfony\Component\HttpFoundation\RequestStack',
            AccessControlService::class => '?' . AccessControlService::class,
            AdmissionNotifier::class => '?' . AdmissionNotifier::class,
            AdmissionStatistics::class => '?' . AdmissionStatistics::class,
            ApplicationAdmission::class => '?' . ApplicationAdmission::class,
            ApplicationData::class => '?' . ApplicationData::class,
            ApplicationManager::class => '?' . ApplicationManager::class,
            AssistantHistoryData::class => '?' . AssistantHistoryData::class,
            ContentModeManager::class => '?' . ContentModeManager::class,
            FileUploader::class => '?' . FileUploader::class,
            FilterService::class => '?' . FilterService::class,
            GeoLocation::class => '?' . GeoLocation::class,
            InterviewCounter::class => '?' . InterviewCounter::class,
            InterviewManager::class => '?' . InterviewManager::class,
            LogService::class => '?' . LogService::class,
            PasswordManager::class => '?' . PasswordManager::class,
            RoleExtension::class => '?' . RoleExtension::class,
            RoleManager::class => '?' . RoleManager::class,
            SbsData::class => '?' . SbsData::class,
            SlackMessenger::class => '?' . SlackMessenger::class,
            SlugMaker::class => '?' . SlugMaker::class,
            Sorter::class => '?' . Sorter::class,
            SurveyManager::class => '?' . SurveyManager::class,
            SurveyNotifier::class => '?' . SurveyNotifier::class,
            UserGroupCollectionManager::class => '?' . UserGroupCollectionManager::class,
            UserRegistration::class => '?' . UserRegistration::class,
            ReversedRoleHierarchy::class => '?' . ReversedRoleHierarchy::class,
        ]);
    }

    /**
     * Tries to get department from the Request and opts to the user's department if none is found.
     * Returns null if none can be found this way.
     * @param Request $request
     * @return Department|null
     */
    public function getDepartment(Request $request): ?Department
    {
        $department = null;
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($this->getUser() !== null) {
                $department = $this->getUser()->getDepartment();
            }
        } else {
            $department = $this->getDoctrine()->getRepository(Department::class)->find($departmentId);
        }
        return $department;
    }

    /**
     * Tries to get semester from the Request and opts to the current if none is found.
     * Returns null if the given ID has no corresponding semester.
     * @param Request $request
     * @return Semester|null
     */
    public function getSemester(Request $request): ?Semester
    {
        $semesterId = $request->query->get('semester');
        if ($semesterId === null) {
            $semester = $this->getCurrentSemester();
        } else {
            $semester = $this->getDoctrine()->getRepository(Semester::class)->find($semesterId);
        }
        return $semester;
    }

    public function getCurrentSemester(): Semester
    {
        return $this->getDoctrine()->getRepository(Semester::class)->findOrCreateCurrentSemester();
    }

    /**
     * 404's if department is null in the request and for the user, or if a wrong department ID is given.
     * @param Request $request
     * @return Department
     */
    public function getDepartmentOrThrow404(Request $request): Department
    {
        $department = $this->getDepartment($request);
        if ($department === null) {
            throw new NotFoundHttpException();
        }
        return $department;
    }

    /**
     * @param Request $request
     * @return Semester
     */
    public function getSemesterOrThrow404(Request $request): Semester
    {
        $semester = $this->getSemester($request);
        if ($semester === null) {
            throw new NotFoundHttpException();
        }
        return $semester;
    }
}
