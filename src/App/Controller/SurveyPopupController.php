<?php


namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SurveyRepository;
use App\Entity\Survey;
use App\Role\Roles;
use App\Service\RoleManager;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SurveyPopupController extends BaseController
{
    public function __construct(
        private SurveyRepository $surveyRepo,
        private RoleManager $roleManager,
        private RequestStack $requestStack,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function nextSurveyAction()
    {
        $survey = null;
        $user = $this->getUser();
        $userShouldSeePopUp = $user !== null &&
            $this->roleManager->userIsGranted($user, Roles::TEAM_MEMBER) &&
            !$user->getReservedFromPopUp() &&
            $user->getLastPopUpTime()->diff(new DateTime())->days >= 1;

        if ($userShouldSeePopUp) {
            $semester = $this->getCurrentSemester();

            if ($semester !== null) {
                $surveys = $this->surveyRepo
                    ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);

                if (!empty($surveys)) {
                    $survey = end($surveys);
                }
            }
        }

        $routeName = $this->requestStack->getMainRequest()->get('_route');
        if (strpos($routeName, "survey_show") !== false) {
            return new Response();
        }
        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
