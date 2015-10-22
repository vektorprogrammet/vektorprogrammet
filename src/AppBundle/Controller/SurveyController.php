<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveySchema;
use AppBundle\Form\Type\SurveySchemaType;
use AppBundle\Form\Type\SurveyAnswerType;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyExecuteType;

/**
 * InterviewController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
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
        dump($survey);
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new SurveyExecuteType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($survey);
            $em->flush();
        }

        dump($form);
        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function createSurveyAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $survey = new Survey();

        $form = $this->createForm(new SurveyType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
    }

    public function showSurveysAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findAll();

        return $this->render('survey/surveys.html.twig', array('surveys' => $surveys));
    }

    public function editSurveyAction(Request $request, Survey $survey)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new SurveyType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @param Survey $survey
     * @return JsonResponse
     */
    public function deleteSurveyAction(Survey $survey)
    {
        try {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($survey);
                $em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.'
            ];
        }

        return new JsonResponse($response);
    }

}
