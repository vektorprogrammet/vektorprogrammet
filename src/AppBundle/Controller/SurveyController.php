<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyExecuteType;

/**
 * SurveyController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends Controller
{
    /**
     * Shows the given survey.
     *
     * @param Request $request
     * @param Survey  $survey
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            $surveyTaken->setTime(new \DateTime());

            if ($surveyTaken->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                if ($survey->isShowCustomFinishPage()) {
                    return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
                }

                $this->addFlash('undersokelse-notice', 'Mottatt svar!');
            } else {
                $this->addFlash('undersokelse-warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),

        ));
    }

    public function showAdminAction(Request $request, Survey $survey)
    {
        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);
        $surveyTaken = $this->get('survey.manager')->predictSurveyTakenAnswers($surveyTaken);

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            $surveyTaken->setTime(new \DateTime());

            if ($surveyTaken->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                $this->addFlash('undersokelse-notice', 'Mottatt svar!');
            } else {
                $this->addFlash('undersokelse-warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show_admin', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function createSurveyAction(Request $request)
    {
        $survey = new Survey();

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    public function copySurveyAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        $surveyClone = $survey->copy();
        $surveyClone->setSemester($semester);

        $form = $this->createForm(SurveyType::class, $surveyClone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($surveyClone);
            $em->flush();

            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    /**
     * @Route(
     *     "/kontrollpanel/undersokelse/admin/{id}",
     *     name="surveys",
     *     methods={"GET"},
     *     defaults={"id": null}
     * )
     *
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSurveysAction(Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findBy(
            ['semester' => $semester], ['id' => 'DESC']
        );
        foreach ($surveys as $survey) {
            $totalAnswered = count($this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findBy(array('survey' => $survey)));
            $survey->setTotalAnswered($totalAnswered);
        }

        return $this->render('survey/surveys.html.twig', array(
            'surveys' => $surveys,
            'semester' => $semester
        ));
    }

    public function editSurveyAction(Request $request, Survey $survey)
    {
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array(
            'form' => $form->createView(),
            'survey' => $survey
        ));
    }

    /**
     * Deletes the given Survey.
     * This method is intended to be called by an Ajax request.
     *
     * @param Survey $survey
     *
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
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }

    public function resultSurveyAction(Survey $survey)
    {
        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $survey->getTextAnswerWithSchoolResults(),
            'survey' => $survey,
        ));
    }

    public function getSurveyResultAction(Survey $survey)
    {
        $surveysTaken = $this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey);
        $validSurveysTaken = array();
        $schools = [];
        foreach ($surveysTaken as $surveyTaken) {
            if (is_null($surveyTaken->getSchool())) {
                continue;
            }
            if ($surveyTaken->isValid()) {
                $validSurveysTaken[] = $surveyTaken;
            }
            if (!in_array($surveyTaken->getSchool()->getName(), $schools)) {
                $schools[] = $surveyTaken->getSchool()->getName();
            }
        }

        //Inject the school question into question array
        $schoolQuestion = array('question_id' => 0, 'question_label' => 'Skole', 'alternatives' => $schools);
        $survey_json = json_encode($survey);
        $survey_decode = json_decode($survey_json, true);
        $survey_decode['questions'][] = $schoolQuestion;

        return new JsonResponse(array('survey' => $survey_decode, 'answers' => $validSurveysTaken));
    }
}
