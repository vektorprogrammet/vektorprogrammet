<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Form\Type\SurveyAdminType;
use AppBundle\Form\Type\SurveyExecuteType;
use AppBundle\Form\Type\SurveySchoolSpecificExecuteType;
use AppBundle\Form\Type\SurveyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * SurveyController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends BaseController
{

    /**
     * Shows the given survey.
     *
     * @param Request $request
     * @param Survey $survey
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        if ($survey->isTeamSurvey()) {
            return $this->redirectToRoute('survey_show_team', array('id' => $survey->getId()));
        }


        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);

        $form = $this->createForm(SurveySchoolSpecificExecuteType::class, $surveyTaken, array(
            'validation_groups' => array('schoolSpecific'),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                if ($survey->isShowCustomFinishPage()) {
                    return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
                }

                $this->addFlash('success', 'Mottatt svar!');
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }
            //New form without previous answers
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
            'teamSurvey' => $survey->isTeamSurvey(),


        ));
    }

    public function showTeamAction(Request $request, Survey $survey)
    {
        $user = $this->getUser();
        if (!$survey->isTeamSurvey()) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        } elseif ($user === null) {
            throw new AccessDeniedException("Dette er en teamundersøkese. Logg inn for å ta den!");
        }
        $surveyTaken = $this->get('survey.manager')->initializeTeamSurveyTaken($survey, $user);
        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $surveyTaken->removeNullAnswers();
            if ($form->isValid()) {
                $allTakenSurveys = $em
                    ->getRepository('AppBundle:SurveyTaken')
                    ->findAllSurveyTakenBySurveyAndUser($survey, $user);


                if (!empty($allTakenSurveys)) {
                    foreach ($allTakenSurveys as $oldTakenSurvey) {
                        $em->remove($oldTakenSurvey);
                    }
                }
                $user->setLastPopUpTime(new \DateTime());
                $em->persist($user);
                $em->persist($surveyTaken);
                $em->flush();


                if ($survey->isShowCustomFinishPage()) {
                    return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
                }
                $this->addFlash('success', 'Mottatt svar!');
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }

            //New form without previous answers
            return $this->redirectToRoute('survey_show_team', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
            'teamSurvey' => $survey->isTeamSurvey(),

        ));
    }

    public function showAdminAction(Request $request, Survey $survey)
    {
        if ($survey->isTeamSurvey()) {
            throw new \InvalidArgumentException("Er team undersøkelse og har derfor ingen admin utfylling");
        }
        $surveyTaken = $this->get('survey.manager')->initializeSurveyTaken($survey);
        $surveyTaken = $this->get('survey.manager')->predictSurveyTakenAnswers($surveyTaken);

        $form = $this->createForm(SurveySchoolSpecificExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();

            if ($form->isValid()) {
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
            'teamSurvey' => $survey->isTeamSurvey(),
        ));
    }

    public function createSurveyAction(Request $request)
    {
        $survey = new Survey();
        $survey->setDepartment($this->getUser()->getDepartment());

        if ($this->get('app.access_control')->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->ensureAccess($survey);
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
        $this->ensureAccess($survey);

        $em = $this->getDoctrine()->getManager();
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();
        $surveyClone = $survey->copy();
        $surveyClone->setSemester($currentSemester);

        if ($this->get('app.access_control')->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }


        $em->flush();

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
     *     "/kontrollpanel/undersokelse/admin",
     *     name="surveys",
     *     methods={"GET"},
     * )
     *
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSurveysAction()
    {
        $semester = $this->getSemesterOrThrow404();
        $department = $this->getDepartmentOrThrow404();


        $surveysWithDepartment = $this->getDoctrine()->getRepository('AppBundle:Survey')->findBy(
            [
                'semester' => $semester,
                'department' => $department,
            ],
            ['id' => 'DESC']
        );


        foreach ($surveysWithDepartment as $survey) {
            $totalAnswered = count($this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findBy(array('survey' => $survey)));
            $survey->setTotalAnswered($totalAnswered);
        }


        $globalSurveys = array();
        if ($this->get('app.access_control')->checkAccess("survey_admin")) {
            $globalSurveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findBy(
                [
                    'semester' => $semester,
                    'department' => null,
                ],
                ['id' => 'DESC']
            );
            foreach ($globalSurveys as $survey) {
                $totalAnswered = count($this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findBy(array('survey' => $survey)));
                $survey->setTotalAnswered($totalAnswered);
            }
        }


        return $this->render('survey/surveys.html.twig', array(
            'surveysWithDepartment' => $surveysWithDepartment,
            'globalSurveys' => $globalSurveys,
            'department' => $department,
            'semester' => $semester,
        ));
    }

    public function editSurveyAction(Request $request, Survey $survey)
    {

        $this->ensureAccess($survey);

        if ($this->get('app.access_control')->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }

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
        $this->ensureAccess($survey);

        $em = $this->getDoctrine()->getManager();
        $em->remove($survey);
        $em->flush();
        $response['success'] = true;

        return new JsonResponse($response);
    }

    public function resultSurveyAction(Survey $survey)
    {
        if ($survey->isConfidential() || !$this->get('app.access_control')->checkAccess("survey_admin")) {
            throw new AccessDeniedException();
        }

        if ($survey->isTeamSurvey()) {
            return $this->render('survey/survey_result.html.twig', array(
                'textAnswers' => $this->get('survey.manager')->getTextAnswerWithTeamResults($survey),
                'survey' => $survey,
                'teamSurvey' => $survey->isTeamSurvey(),
            ));
        }

        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $this->get('survey.manager')->getTextAnswerWithSchoolResults($survey),
            'survey' => $survey,
            'teamSurvey' => $survey->isTeamSurvey(),

        ));
    }

    public function getSurveyResultAction(Survey $survey)
    {
        return new JsonResponse($this->get('survey.manager')->surveyResultToJson($survey));
    }


    public function toggleReservePopUpAction()
    {
        $this->get('survey.manager')->toggleReservePopUp($this->getUser());
        return new JsonResponse();
    }

    public function closePopUpAction()
    {
        $this->get('survey.manager')->closePopUp($this->getUser());
        return new JsonResponse();
    }

    /**
     * @param Survey $survey
     *
     * @throws AccessDeniedException
     */
    private function ensureAccess(Survey $survey)
    {
        $user = $this->getUser();

        $isSurveyAdmin = $this->get('app.access_control')->checkAccess("survey_admin");
        $isSameDepartment = $survey->getDepartment() === $user->getDepartment();

        if ($isSameDepartment || $isSurveyAdmin) {
            return;
        }

        throw new AccessDeniedException();


    }
}
