<?php
/**
 * Created by IntelliJ IDEA.
 * User: Amir Ahmed
 * Date: 20.09.2018
 * Time: 19:34
 */

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
            $user->getLastPopUp()->diff(new \DateTime())->d >= 0;

        if ($userShouldSeePopUp) {
            $surveys = $this->getDoctrine()
                    ->getRepository('AppBundle:Survey')
                    ->findOneByUserNotTaken($this->getUser());

            if (!empty($surveys)) {
                $survey=$surveys[0];
            }
        }

        return $this->render("base/popup_lower.twig",
            array('survey' => $survey));
    }
}
