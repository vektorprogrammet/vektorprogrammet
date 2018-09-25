<?php
/**
 * Created by IntelliJ IDEA.
 * User: Amir Ahmed
 * Date: 20.09.2018
 * Time: 19:34
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TwigSurveyController extends Controller
{

    public function nextSurveyAction(){

        $survey = null;
        $user = $this->getUser();

        if($user !== null && !$user->getReservedPopUp()&& $user->getLastPopUp()->diff(new \DateTime())->days > 1) {
                $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findOneByUserNotTaken($this->getUser());
                if(!empty($surveys)){
                   $survey=$surveys[0];
                }
        }


        return $this->render("base/popup_lower.twig",
            array('survey' => $survey));
    }
}