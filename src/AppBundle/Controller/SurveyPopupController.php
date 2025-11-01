<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Survey;
use AppBundle\Repository\Contract\SurveyRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\RoleManagerInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SurveyPopupController extends BaseController
{
    private $roleManager;
    private $entityManager;
    private $surveyRepository;
    private $requestStack;

    /**
     * @param RoleManagerInterface $roleManager
     * @param EntityManagerInterface $entityManager
     * @param SurveyRepositoryInterface $surveyRepository
     * @param RequestStack $requestStack
     */
    public function __construct(
        RoleManagerInterface $roleManager,
        EntityManagerInterface $entityManager,
        SurveyRepositoryInterface $surveyRepository,
        RequestStack $requestStack
    ) {
        $this->roleManager = $roleManager;
        $this->entityManager = $entityManager;
        $this->surveyRepository = $surveyRepository;
        $this->requestStack = $requestStack;
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
                $surveys = $this->surveyRepository->findAllNotTakenByUserAndSemester($this->getUser(), $semester);

                if (!empty($surveys)) {
                    $survey = end($surveys);
                }
            }
        }

        $routeName = $this->requestStack->getMasterRequest()->get('_route');
        if (strpos($routeName, "survey_show") !== false) {
            return new Response();
        }
        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
