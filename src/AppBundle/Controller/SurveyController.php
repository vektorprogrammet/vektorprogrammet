<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyLinkClick;
use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\User;
use AppBundle\Form\Type\SurveyAdminType;
use AppBundle\Form\Type\SurveyExecuteType;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Service\AccessControlService;
use AppBundle\Service\SurveyManager;
use AppBundle\Utils\CsvUtil;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $surveyTaken = $this->get(SurveyManager::class)->initializeSurveyTaken($survey);
        if ($survey->getTargetAudience() === Survey::$SCHOOL_SURVEY || $survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY) {
            $form = $this->createForm(SurveyExecuteType::class, $surveyTaken, array(
                'validation_groups' => array('schoolSpecific'),
            ));
        } elseif ($survey->getTargetAudience() === Survey::$TEAM_SURVEY) {
            return $this->showUserAction($request, $survey);
        } else {
            $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        }
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($surveyTaken);
                $em->flush();

                $this->addFlash('success', 'Mottatt svar!');

                return $this->render('survey/finish_page.html.twig', [
                        'content' => $survey->getFinishPageContent(),
                    ]);
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');
            }
            //New form without previous answers
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
            'surveyTargetAudience' => $survey->getTargetAudience(),
            'userIdentified' => false,

        ));
    }


    /**
     * @param Request $request
     * @param Survey $survey
     * @param string $userid
     *
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function showIdAction(Request $request, Survey $survey, string $userid)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository(SurveyNotification::class)->findByUserIdentifier($userid);


        if ($notification === null) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        $sameSurvey = $notification->getSurveyNotificationCollection()->getSurvey() == $survey;

        if (!$sameSurvey) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }


        $surveyLinkClick = new SurveyLinkClick();
        $surveyLinkClick->setNotification($notification);
        $em->persist($surveyLinkClick);
        $em->flush();

        $user = $notification->getUser();

        return $this->showUserMainAction($request, $survey, $user, $userid);
    }


    public function showUserAction(Request $request, Survey $survey)
    {
        $user = $this->getUser();
        if ($survey->getTargetAudience() === Survey::$SCHOOL_SURVEY) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        } elseif ($user === null) {
            throw new AccessDeniedException("Logg inn for å ta undersøkelsen!");
        }
        return $this->showUserMainAction($request, $survey, $user);
    }

    private function showUserMainAction(Request $request, Survey $survey, User $user, string $identifier = null)
    {
        $surveyTaken = $this->get(SurveyManager::class)->initializeUserSurveyTaken($survey, $user);
        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY) {
            $assistantHistory = $em->getRepository(AssistantHistory::class)->findMostRecentByUser($user);

            if (empty($assistantHistory)) {
                return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
            }
            $assistantHistory = $assistantHistory[0];
            $school = $assistantHistory->getSchool();
            $surveyTaken->setSchool($school);
        }


        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            if ($form->isValid()) {
                $allTakenSurveys = $em
                    ->getRepository('AppBundle:SurveyTaken')
                    ->findAllBySurveyAndUser($survey, $user);

                if (!empty($allTakenSurveys)) {
                    foreach ($allTakenSurveys as $oldTakenSurvey) {
                        $em->remove($oldTakenSurvey);
                    }
                }

                $user->setLastPopUpTime(new DateTime());
                $em->persist($user);
                $em->persist($surveyTaken);
                $em->flush();

                $this->addFlash('success', 'Mottatt svar!');
                return $this->render('survey/finish_page.html.twig', [
                    'content' => $survey->getFinishPageContent(),
                ]);
            } else {
                $this->addFlash('warning', 'Svaret ditt ble ikke sendt! Du må fylle ut alle obligatoriske felter.');

                if ($survey->getTargetAudience() === Survey::$TEAM_SURVEY || ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY  && $identifier !== null)) {
                    $route = 'survey_show_user';
                } else {
                    return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
                }

                $parameters = array('id' => $survey->getId());
                if ($identifier !== null) {
                    $parameters += array('userid' => $identifier);
                }

                //New form without previous answers
                return $this->redirectToRoute($route, $parameters);
            }
        }

        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),
            'surveyTargetAudience' => $survey->getTargetAudience(),
            'userIdentified' => true,

        ));
    }

    public function showAdminAction(Request $request, Survey $survey)
    {
        if ($survey->getTargetAudience() === Survey::$TEAM_SURVEY) {
            throw new \InvalidArgumentException("Er team undersøkelse og har derfor ingen admin utfylling");
        }
        $surveyTaken = $this->get(SurveyManager::class)->initializeSurveyTaken($survey);
        $surveyTaken = $this->get(SurveyManager::class)->predictSurveyTakenAnswers($surveyTaken);

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
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
            'surveyTargetAudience' => $survey->getTargetAudience(),
            'userIdentified' => false,

        ));
    }

    public function createSurveyAction(Request $request)
    {
        $survey = new Survey();
        $survey->setDepartment($this->getUser()->getDepartment());

        if ($this->get(AccessControlService::class)->checkAccess("survey_admin")) {
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

        $surveyClone = $survey->copy();

        $em = $this->getDoctrine()->getManager();
        $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();
        $surveyClone->setSemester($currentSemester);

        if ($this->get(AccessControlService::class)->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $surveyClone);
        } else {
            $form = $this->createForm(SurveyType::class, $surveyClone);
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
            'survey' => $surveyClone
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
            $totalAnswered = count($this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey));
            $survey->setTotalAnswered($totalAnswered);
        }


        $globalSurveys = array();
        if ($this->get(AccessControlService::class)->checkAccess("survey_admin")) {
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

        if ($this->get(AccessControlService::class)->checkAccess("survey_admin")) {
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

    /**
     * The html page showing results from a survey.
     *
     * @param Survey $survey
     * @return Response
     * @see SurveyController::getSurveyResultAction
     */
    public function resultSurveyAction(Survey $survey)
    {
        $this->ensureAccess($survey);

        if ($survey->getTargetAudience() === Survey::$SCHOOL_SURVEY) {
            $textAnswers = $this->get(SurveyManager::class)
                ->getTextAnswerWithSchoolResults($survey);
        } else {
            $textAnswers = $this->get(SurveyManager::class)
                ->getTextAnswerWithTeamResults($survey);
        }

        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $textAnswers,
            'survey' => $survey,
            'surveyTargetAudience' => $survey->getTargetAudience(),
        ));
    }

    /**
     * Answer data from the given survey, formated as a json response.
     * Part of the api used by the front-end.
     *
     * @param Survey $survey
     * @return JsonResponse
     */
    public function getSurveyResultAction(Survey $survey)
    {
        $this->ensureAccess($survey);
        return new JsonResponse($this->get(SurveyManager::class)->surveyResultToJson($survey));
    }

    /**
     * Responds with a csv-file containing a table of all responses to the given survey.
     * Not a part of the api, but rather a front-facing feature.
     *
     * @param Survey $survey
     * @return Response
     */
    public function getSurveyResultCSVAction(Survey $survey):Response
    {
        $this->ensureAccess($survey);
        $sm = $this->get(SurveyManager::class);
        $csv_string = $sm->surveyResultsToCsv($survey);
        return CsvUtil::makeCsvResponse($csv_string);
    }

    public function toggleReservedFromPopUpAction()
    {
        $user = $this->getUser();
        if ($user === null) {
            return null;
        }

        $this->get(SurveyManager::class)->toggleReservedFromPopUp($this->getUser());

        return new JsonResponse();
    }

    public function closePopUpAction()
    {
        $user = $this->getUser();
        $user->setLastPopUpTime(new DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new JsonResponse();
    }


    /**
     * @param Survey $survey
     *
     * Throws unless you are in the same department as the survey, or you are a survey_admin.
     * If the survey is confidential, only survey_admin has access.
     *
     * @throws AccessDeniedException
     */
    private function ensureAccess(Survey $survey)
    {
        $user = $this->getUser();

        $isSurveyAdmin = $this->get(AccessControlService::class)->checkAccess("survey_admin");
        $isSameDepartment = $survey->getDepartment() === $user->getDepartment();

        if ($survey->isConfidential() && !$isSurveyAdmin) {
            throw new AccessDeniedException();
        }

        if ($isSameDepartment || $isSurveyAdmin) {
            return;
        }

        throw new AccessDeniedException();
    }
}
