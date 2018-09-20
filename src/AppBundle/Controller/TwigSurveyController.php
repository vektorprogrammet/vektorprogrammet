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

        if($this->getUser()!==null) {

            try {
                $survey = $this->getDoctrine()->getRepository('AppBundle:Survey')->findOneByUserNotTaken($this->getUser());
            } catch (\Doctrine\ORM\NonUniqueResultException $e) {
                // This should not happen
                throw new $e;
            }

        }

        dump($survey);

        return $this->render("base/popup_lower.twig",
            array('survey' => $survey));
    }
}