<?php

namespace App\Survey\Controller;

use App\Identity\Infrastructure\Entity\User;
use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\RoleManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SurveyPopupController extends BaseController
{
    public function __construct(
        private readonly SurveyRepository $surveyRepo,
        private readonly RoleManager $roleManager,
        private readonly RequestStack $requestStack,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function nextSurveyAction()
    {
        $survey = null;
        $user = $this->getUser();
        if ($user instanceof User
            && $this->roleManager->userIsGranted($user, Roles::TEAM_MEMBER)
            && !$user->getReservedFromPopUp()
            && $user->getLastPopUpTime()->diff(new \DateTime())->days >= 1
        ) {
            $semester = $this->getCurrentSemester();
            $surveys = $this->surveyRepo
                ->findAllNotTakenByUserAndSemester($user, $semester);

            if ($surveys !== []) {
                $survey = end($surveys);
            }
        }

        $routeName = $this->requestStack->getMainRequest()->get('_route');
        if (str_contains((string) $routeName, 'survey_show')) {
            return new Response();
        }

        return $this->render(
            'base/popup_lower.twig',
            ['survey' => $survey]
        );
    }
}
