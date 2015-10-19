<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveySchema;
use AppBundle\Form\Type\SurveySchemaType;
use AppBundle\Form\Type\SurveyType;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 *
 * @package AppBundle\Controller
 */
class SurveyController extends Controller
{
    /**
     * Shows the given survey.
     * @param Request $request
     * @param Survey $survey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new surveyType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($survey);
            $em->flush();
        }

        return $this->render('survey/survey.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
