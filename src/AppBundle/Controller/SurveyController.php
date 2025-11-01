<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyLinkClick;
use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\User;
use AppBundle\Form\Type\SurveyAdminType;
use AppBundle\Form\Type\SurveyExecuteType;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\AccessControlServiceInterface;
use AppBundle\Service\Contract\SurveyManagerInterface;
use AppBundle\Service\Contract\SurveyExecutionServiceInterface;
use AppBundle\Utils\CsvUtil;
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
    private $surveyManager;
    private $surveyExecutionService;
    private $accessControlService;
    private $entityManager;
    private $assistantHistoryRepository;
    private $semesterRepository;

    /**
     * @param SurveyManagerInterface $surveyManager
     * @param SurveyExecutionServiceInterface $surveyExecutionService
     * @param AccessControlServiceInterface $accessControlService
     * @param EntityManagerInterface $entityManager
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param SemesterRepositoryInterface $semesterRepository
     */
    public function __construct(
        SurveyManagerInterface $surveyManager,
        SurveyExecutionServiceInterface $surveyExecutionService,
        AccessControlServiceInterface $accessControlService,
        EntityManagerInterface $entityManager,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        SemesterRepositoryInterface $semesterRepository
    ) {
        $this->surveyManager = $surveyManager;
        $this->surveyExecutionService = $surveyExecutionService;
        $this->accessControlService = $accessControlService;
        $this->entityManager = $entityManager;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->semesterRepository = $semesterRepository;
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
                $this->entityManager->persist($surveyTaken);
                $this->entityManager->flush();

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
        $executionResult = $this->surveyExecutionService->executeSurvey($survey, null, $userid);
        $surveyTaken = $executionResult['surveyTaken'];
        $user = $executionResult['user'] ?? null;

        if ($surveyTaken === null || $user === null) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

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
        $executionResult = $this->surveyExecutionService->executeSurvey($survey, $user, $identifier);
        $surveyTaken = $executionResult['surveyTaken'];

        if ($surveyTaken === null || ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY && $executionResult['school'] === null)) {
            return $this->redirectToRoute('survey_show', array('id' => $survey->getId()));
        }

        $form = $this->createForm(SurveyExecuteType::class, $surveyTaken);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $surveyTaken->removeNullAnswers();
            if ($form->isValid()) {
                $this->surveyExecutionService->processSubmission($survey, $surveyTaken, $user);

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
                $this->entityManager->persist($surveyTaken);
                $this->entityManager->flush();

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
            if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
                throw new AccessDeniedException();
            }
            $this->entityManager->persist($survey);
            $this->entityManager->flush();

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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }

        $surveyClone = $survey->copy();

        $currentSemester = $this->semesterRepository->findOrCreateCurrentSemester();
        $surveyClone->setSemester($currentSemester);

        if ($this->accessControlService->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $surveyClone);
        } else {
            $form = $this->createForm(SurveyType::class, $surveyClone);
        }

        $this->entityManager->flush();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($surveyClone);
            $this->entityManager->flush();

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


        $surveysWithDepartment = $this->entityManager->getRepository(Survey::class)->findBy(
            [
                'semester' => $semester,
                'department' => $department,
            ],
            ['id' => 'DESC']
        );
        foreach ($surveysWithDepartment as $survey) {
            $stats = $this->surveyExecutionService->calculateStatistics($survey);
            $survey->setTotalAnswered($stats['totalAnswered']);
        }


        $globalSurveys = array();
        if ($this->accessControlService->checkAccess("survey_admin")) {
            $globalSurveys = $this->entityManager->getRepository(Survey::class)->findBy(
                [
                    'semester' => $semester,
                    'department' => null,
                ],
                ['id' => 'DESC']
            );
            foreach ($globalSurveys as $survey) {
                $stats = $this->surveyExecutionService->calculateStatistics($survey);
                $survey->setTotalAnswered($stats['totalAnswered']);
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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }

        if ($this->accessControlService->checkAccess("survey_admin")) {
            $form = $this->createForm(SurveyAdminType::class, $survey);
        } else {
            $form = $this->createForm(SurveyType::class, $survey);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($survey);
            $this->entityManager->flush();

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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }

        $this->entityManager->remove($survey);
        $this->entityManager->flush();
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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }

        $results = $this->surveyExecutionService->prepareResults($survey);

        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $results['textAnswers'],
            'survey' => $survey,
            'surveyTargetAudience' => $results['surveyTargetAudience'],
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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }
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
        if (!$this->surveyExecutionService->checkAccess($this->getUser(), $survey)) {
            throw new AccessDeniedException();
        }
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
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new JsonResponse();
    }


}
