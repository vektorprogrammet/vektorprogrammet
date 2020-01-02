<?php


namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use AppBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SurveyPopupController extends Controller
{
    public function nextSurveyAction()
    {
        $survey = null;
        $user = $this->getUser();
        $userShouldSeePopUp = $user !== null &&
            $this->get(RoleManager::class)->userIsGranted($user, Roles::TEAM_MEMBER) &&
            !$user->getReservedFromPopUp() &&
            $user->getLastPopUpTime()->diff(new \DateTime())->days >= 1;

        if ($userShouldSeePopUp) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();

            $surveys = $this->getDoctrine()
                ->getRepository('AppBundle:Survey')
                ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);

            if (!empty($surveys)) {
                $survey = end($surveys);
            }
        }


        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');
        if (strpos($routeName, "survey_show") !== false) {
            return new Response();
        }
        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
