<?php


namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use AppBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwigSurveyController extends Controller
{
    public function nextSurveyAction(Request $request)
    {
        $survey = null;
        $user = $this->getUser();
        $userShouldSeePopUp = $user !== null &&
            $this->get("app.roles")->userIsGranted($user, Roles::TEAM_MEMBER) &&
            !$user->getReservedPopUp()&&
            $user->getLastPopUp()->diff(new \DateTime())->days >= 1;

        if ($userShouldSeePopUp) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();

            $surveys = $this->getDoctrine()
                    ->getRepository('AppBundle:Survey')
                    ->findOneByUserNotTaken($this->getUser(), $semester);

            if (!empty($surveys)) {
                $survey=end($surveys);
            }
        }


        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');
        if(strpos($routeName, "survey_show") !== false){
            return new Response();

        }
        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
