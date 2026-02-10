<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SurveyNotificationRepository;
use App\Entity\Repository\SurveyRepository;
use App\Entity\Repository\SurveyTakenRepository;
use App\Entity\Semester;
use App\Entity\Survey;
use App\Entity\SurveyLinkClick;
use App\Entity\SurveyNotification;
use App\Entity\SurveyTaken;
use App\Entity\User;
use App\Form\Type\SurveyAdminType;
use App\Form\Type\SurveyExecuteType;
use App\Form\Type\SurveyType;
use App\Service\AccessControlService;
use App\Service\SurveyManager;
use App\Utils\CsvUtil;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * SurveyController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends BaseController
{
    public function __construct(
        private SurveyNotificationRepository $surveyNotificationRepo,
        private AssistantHistoryRepository $assistantHistoryRepo,
        private SurveyTakenRepository $surveyTakenRepo,
        private SurveyRepository $surveyRepo,
        private SurveyManager $surveyManager,
        private AccessControlService $accessControlService,
        private EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }


    /**
     * Shows the given survey.
     *
     * @param Request $request
     * @param Survey $survey
     *
     * @return Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        $surveyTaken = $this->surveyManager->initializeSurveyTaken($survey);
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
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($surveyTaken);
                $this->em->flush();

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
     * @return RedirectResponse
     */
    public function showIdAction(Request $request, Survey $survey, string $userid)
    {
        $notification = $this->surveyNotificationRepo->findByUserIdentifier($userid);


        if ($notification === null) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        $sameSurvey = $notification->getSurveyNotificationCollection()->getSurvey() == $survey;

        if (!$sameSurvey) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }


        $surveyLinkClick = new SurveyLinkClick();
        $surveyLinkClick->setNotification($notification);
        $this->em->persist($surveyLinkClick);
        $this->em->flush();

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
        $surveyTaken = $this->surveyManager->initializeUserSurveyTaken($survey, $user);
        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY) {
            $assistantHistory = $this->assistantHistoryRepo->findMostRecentByUser($user);

            if (empty($assistantHistory)) {
                return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
            }
            $assistantHistory = $assistantHistory[0];
            $school = $assistantHistory->getSchool();
            $surveyTaken->setSchool($school);
        }


        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            if ($form->isSubmitted() && $form->isValid()) {
                $allTakenSurveys = $this->surveyTakenRepo
                    ->findAllBySurveyAndUser($survey, $user);

                if (!empty($allTakenSurveys)) {
                    foreach ($allTakenSurveys as $oldTakenSurvey) {
                        $this->em->remove($oldTakenSurvey);
                    }
                }

                $user->setLastPopUpTime(new DateTime());
                $this->em->persist($user);
                $this->em->persist($surveyTaken);
                $this->em->flush();

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
            throw new InvalidArgumentException("Er team undersøkelse og har derfor ingen admin utfylling");
        }
        $surveyTaken = $this->surveyManager->initializeSurveyTaken($survey);
        $surveyTaken = $this->surveyManager->predictSurveyTakenAnswers($surveyTaken);

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($surveyTaken);
                $this->em->flush();

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

        if ($this->accessControlService->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ensureAccess($survey);
            $this->em->persist($survey);
            $this->em->flush();

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

        $currentSemester = $this->getCurrentSemester();
        $surveyClone->setSemester($currentSemester);

        if ($this->accessControlService->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $surveyClone);
        } else {
            $form = $this->createForm(SurveyType::class, $surveyClone);
        }

        $this->em->flush();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($surveyClone);
            $this->em->flush();

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
     * @param Request $request
     * @return Response
     */
    public function showSurveysAction(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);


        $surveysWithDepartment = $this->surveyRepo->findBy(
            [
                'semester' => $semester,
                'department' => $department,
            ],
            ['id' => 'DESC']
        );
        foreach ($surveysWithDepartment as $survey) {
            $totalAnswered = count($this->surveyTakenRepo->findAllTakenBySurvey($survey));
            $survey->setTotalAnswered($totalAnswered);
        }


        $globalSurveys = array();
        if ($this->accessControlService->checkAccess("survey_admin")) {
            $globalSurveys = $this->surveyRepo->findBy(
                [
                    'semester' => $semester,
                    'department' => null,
                ],
                ['id' => 'DESC']
            );
            foreach ($globalSurveys as $survey) {
                $totalAnswered = count($this->surveyTakenRepo->findBy(array('survey' => $survey)));
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

        if ($this->accessControlService->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($survey);
            $this->em->flush();

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

        $this->em->remove($survey);
        $this->em->flush();
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
            $textAnswers = $this->surveyManager
                ->getTextAnswerWithSchoolResults($survey);
        } else {
            $textAnswers = $this->surveyManager
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
        return new JsonResponse($this->surveyManager->surveyResultToJson($survey));
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
        $csv_string = $this->surveyManager->surveyResultsToCsv($survey);
        return CsvUtil::makeCsvResponse($csv_string);
    }

    public function toggleReservedFromPopUpAction()
    {
        $user = $this->getUser();
        if ($user === null) {
            return null;
        }

        $this->surveyManager->toggleReservedFromPopUp($this->getUser());

        return new JsonResponse();
    }

    public function closePopUpAction()
    {
        $user = $this->getUser();
        $user->setLastPopUpTime(new DateTime());
        $this->em->persist($user);
        $this->em->flush();
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

        $isSurveyAdmin = $this->accessControlService->checkAccess("survey_admin");
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
