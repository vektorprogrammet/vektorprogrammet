<?php


namespace AppBundle\Controller;

use AppBundle\Role\Roles;
use AppBundle\Service\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TwigSurveyController extends Controller
{
    public function nextSurveyAction()
    {
        $survey = null;
        $user = $this->getUser();
        $userShouldSeePopUp = $user !== null &&
            $this->get("app.roles")->userIsGranted($user, Roles::TEAM_MEMBER) &&
            !$user->getReservedPopUp()&&
            $user->getLastPopUp()->diff(new \DateTime())->days >= 1;

        if ($userShouldSeePopUp) {
            $surveys = $this->getDoctrine()
                    ->getRepository('AppBundle:Survey')
                    ->findOneByUserNotTaken($this->getUser());

            if (!empty($surveys)) {
                $survey=end($surveys);
            }
        }

        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
