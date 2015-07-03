<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SurveyPupil;
use AppBundle\Entity\SurveyTeacher;
use AppBundle\Form\Type\SurveyPupilType;
use AppBundle\Form\Type\SurveyTeacherType;

class SurveyController extends Controller
{

    public function getName()
    {
        return 'surveySchema'; // This must be unique
    }
    public function showPupilAction(Request $request){
        //Creates a new surveyPupil entity
        $survey = new SurveyPupil();
        // Create the form
        $form = $this->createForm(new SurveyPupilType(), $survey);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            //updates the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();
            //Render a simple feedback page, that informs that the survey was sent.
            return $this->render('survey/survey_completed.html.twig');
        }

        // Render the view
        return $this->render('survey/survey.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function showTeacherAction(Request $request){
        //Creates a new surveyTeacher entity
        $survey = new SurveyTeacher();
        // Create the form
        $form = $this->createForm(new SurveyTeacherType(), $survey);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            //Updates the database with the information from the form.
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();
            //Render a simple feedback page, that informs that the survey was sent.
            return $this->render('survey/survey_completed.html.twig');
        }

        // Render the view
        return $this->render('survey/survey.html.twig', array(
            'form' => $form->createView()
        ));

    }
}