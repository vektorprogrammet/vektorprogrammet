<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Event\InterviewConductedEvent;
use AppBundle\Event\InterviewEvent;
use AppBundle\Form\InterviewNewTimeType;
use AppBundle\Form\Type\AddCoInterviewerType;
use AppBundle\Form\Type\ApplicationInterviewType;
use AppBundle\Form\Type\CancelInterviewConfirmationType;
use AppBundle\Form\Type\CreateInterviewType;
use AppBundle\Form\Type\ScheduleInterviewType;
use AppBundle\Role\ReversedRoleHierarchy;
use AppBundle\Role\Roles;
use AppBundle\Service\ApplicationManager;
use AppBundle\Service\InterviewManager;
use AppBundle\Service\Contract\InterviewSchedulingServiceInterface;
use DateTime;
use InvalidArgumentException;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 */
class InterviewController extends BaseController
{
    private $interviewManager;
    private $applicationManager;
    private $interviewSchedulingService;
    private $teamRepository;
    private $userRepository;
    private $applicationRepository;
    private $entityManager;
    private $eventDispatcher;
    private $reversedRoleHierarchy;

    /**
     * @param InterviewManagerInterface $interviewManager
     * @param ApplicationManagerInterface $applicationManager
     * @param InterviewSchedulingServiceInterface $interviewSchedulingService
     * @param TeamRepositoryInterface $teamRepository
     * @param UserRepositoryInterface $userRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ReversedRoleHierarchy $reversedRoleHierarchy
     */
    public function __construct(
        InterviewManagerInterface $interviewManager,
        ApplicationManagerInterface $applicationManager,
        InterviewSchedulingServiceInterface $interviewSchedulingService,
        TeamRepositoryInterface $teamRepository,
        UserRepositoryInterface $userRepository,
        ApplicationRepositoryInterface $applicationRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ReversedRoleHierarchy $reversedRoleHierarchy
    ) {
        $this->interviewManager = $interviewManager;
        $this->applicationManager = $applicationManager;
        $this->interviewSchedulingService = $interviewSchedulingService;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->applicationRepository = $applicationRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->reversedRoleHierarchy = $reversedRoleHierarchy;
    }

    /**
     * @Route("/kontrollpanel/intervju/conduct/{id}",
     *     name="interview_conduct",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     *     )
     *
     * @param Request $request
     * @param Application $application
     *
     * @return RedirectResponse|Response
     */
    public function conductAction(Request $request, Application $application)
    {
        if ($application->getInterview() === null) {
            throw $this->createNotFoundException();
        }
        $department = $this->getUser()->getDepartment();
        $teams = $this->teamRepository->findActiveByDepartment($department);

        if ($this->getUser() === $application->getUser()) {
            return $this->render('error/control_panel_error.html.twig', array('error' => 'Du kan ikke intervjue deg selv'));
        }

        // If the interview has not yet been conducted, create up to date answer objects for all questions in schema
        $interview = $this->interviewManager->initializeInterviewAnswers($application->getInterview());

        // Only admin and above, or the assigned interviewer, or the co interviewer should be able to conduct an interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ApplicationInterviewType::class, $application, array(
            'validation_groups' => array('interview'),
            'teams' => $teams,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isNewInterview = !$interview->getInterviewed();
            $interview->setCancelled(false);

            $this->entityManager->persist($interview);
            $this->entityManager->flush();
            if ($isNewInterview && $form->get('saveAndSend')->isClicked()) {
                $this->interviewSchedulingService->conductInterview($interview, $application);
            }

            return $this->redirectToRoute('applications_show_interviewed', array(
                'semester' => $application->getSemester()->getId(),
                'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
            ));
        }

        return $this->render('interview/conduct.html.twig', array(
            'application' => $application,
            'department' => $department,
            'teams' => $teams,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Interview $interview
     *
     * @return RedirectResponse
     */
    public function cancelAction(Interview $interview)
    {
        $interview->setCancelled(true);
        $this->entityManager->persist($interview);
        $this->entityManager->flush();

        return $this->redirectToRoute('applications_show_assigned');
    }

    /**
     * Shows the given interview.
     *
     * @param Application $application
     *
     * @return Response
     */
    public function showAction(Application $application)
    {
        if (null === $interview = $application->getInterview()) {
            throw $this->createNotFoundException('Interview not found.');
        }

        // Only accessible for admin and above, or team members belonging to the same department as the interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview) ||
            $this->getUser() === $application->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('interview/show.html.twig', array('interview' => $interview,
            'application' => $application
        ));
    }

    /**
     * Deletes the given interview.
     *
     * @param Interview $interview
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteInterviewAction(Interview $interview, Request $request)
    {
        $interview->getApplication()->setInterview(null);

        $this->entityManager->remove($interview);
        $this->entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Deletes a bulk of interviews.
     * Takes a list of application ids through a form POST request, and deletes the interviews associated with them.
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteInterviewAction(Request $request)
    {
        // Get the ids from the form
        $applicationIds = $request->request->get('application')['id'];

        // Get the application objects
        $applications = $this->applicationRepository->findBy(array('id' => $applicationIds));

        // Delete the interviews using service
        $this->interviewSchedulingService->bulkDeleteInterviews($applications);

        // AJAX response
        return new JsonResponse(array(
            'success' => true,
        ));
    }

    /**
     * Shows and handles the submission of the schedule interview form.
     * This method can also send an email to the applicant with the info from the submitted form.
     *
     * @param Request $request
     * @param Application $application
     *
     * @return Response
     */
    public function scheduleAction(Request $request, Application $application)
    {
        if (null === $interview = $application->getInterview()) {
            throw $this->createNotFoundException('Interview not found.');
        }
        // Only admin and above, or the assigned interviewer should be able to book an interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        // Set the default data for the form
        $defaultData = $this->interviewManager->getDefaultScheduleFormData($interview);

        $form = $this->createForm(ScheduleInterviewType::class, $defaultData);

        $form->handleRequest($request);

        $data = $form->getData();
        $mapLink = $data['mapLink'] ?? null;
        if ($form->isSubmitted()) {
            if ($mapLink && !(strpos($mapLink, 'http') === 0)) {
                $mapLink = 'http://' . $mapLink;
            }
        }
        $invalidMapLink = $form->isSubmitted() && !empty($mapLink) && !$this->interviewSchedulingService->validateMapLink($mapLink);
        if ($invalidMapLink) {
            $this->addFlash('danger', 'Kartlinken er ikke gyldig');
        } elseif ($form->isSubmitted() && $form->isValid()) {
            $data['mapLink'] = $mapLink;
            $this->interviewSchedulingService->scheduleInterview($interview, $data);

            if ($form->get('preview')->isClicked()) {
                return $this->render('interview/preview.html.twig', array(
                    'interview' => $interview,
                    'data' => $data,
                ));
            }

            // Send email if the send button was clicked
            if ($form->get('saveAndSend')->isClicked()) {
                $this->interviewSchedulingService->sendScheduleEmail($interview, $data);
            }

            return $this->redirectToRoute('applications_show_assigned', array('department' => $application->getDepartment()->getId(), 'semester' => $application->getSemester()->getId()));
        }

        return $this->render('interview/schedule.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview,
            'application' => $application,
        ));
    }


    /**
     * Renders and handles the submission of the assign interview form.
     * This method is used to create a new interview, or update it, and assign it to the given application.
     * It sets the interviewer and interview schema according to the form.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function assignAction(Request $request, $id = null)
    {
        if ($id === null) {
            throw $this->createNotFoundException();
        }
        $application = $this->applicationRepository->find($id);
        $user = $application->getUser();
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);

        $form = $this->createForm(CreateInterviewType::class, $application, [
            'roles' => $roles
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schema = $application->getInterview()->getInterviewSchema();
            $this->interviewSchedulingService->assignInterviewer($this->getUser(), $application, $schema);

            return new JsonResponse(
                array('success' => true)
            );
        }

        return new JsonResponse(
            array(
                'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                    'form' => $form->createView(),
                )),
            )
        );
    }

    /**
     * This method has the same purpose as assignAction, but assigns a bulk of applications at once.
     * It does not use the normal form validation routine, but manually updates each application.
     * This is because in addition to the standard form fields given by assignInterviewType, a list of application ids
     * are given by the bulk form checkboxes (see admission_admin twigs).
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function bulkAssignAction(Request $request)
    {
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $form = $this->createForm(CreateInterviewType::class, null, [
            'roles' => $roles
        ]);

        if ($request->isMethod('POST')) {
            // Get the info from the form
            $data = $request->request->all();
            // Get objects from database
            $interviewer = $this->userRepository->findOneBy(array('id' => $data['interview']['interviewer']));
            $schema = $this->entityManager->getRepository(InterviewSchema::class)->findOneBy(array('id' => $data['interview']['interviewSchema']));
            $applications = $this->applicationRepository->findBy(array('id' => $data['application']['id']));

            // Bulk assign interviews
            $this->interviewSchedulingService->bulkAssignInterviews($interviewer, $applications, $schema);

            $this->addFlash('success', 'Søknadene ble fordelt til ' . $interviewer);

            return new JsonResponse(array(
                'success' => true,
                'request' => $request->request->all(),
            ));
        }

        return new JsonResponse(array(
            'form' => $this->renderView('interview/assign_interview_form.html.twig', array(
                'form' => $form->createView(),
            )),
        ));
    }

    /**
     * @param Interview $interview
     *
     * @return Response
     */
    public function acceptByResponseCodeAction(Interview $interview)
    {
        $this->interviewSchedulingService->processInterviewResponse($interview, 'accept');

        $formattedDate = $interview->getScheduled()->format('d. M');
        $formattedTime = $interview->getScheduled()->format('H:i');
        $room = $interview->getRoom();

        $successMessage = "Takk for at du aksepterte intervjutiden. Da sees vi $formattedDate klokka $formattedTime i $room!";
        $this->addFlash('success', $successMessage);

        if ($interview->getUser() === $this->getUser()) {
            return $this->redirectToRoute("my_page");
        }

        return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
    }

    /**
     * @param Request $request
     * @param Interview $interview
     *
     * @return Response
     */
    public function requestNewTimeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(InterviewNewTimeType::class, $interview, array(
            "validation_groups" => array("newTimeRequest")
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->interviewSchedulingService->processInterviewResponse($interview, 'request_new_time');
            $this->addFlash('success', "Forspørsel om ny intervjutid er sendt. Vi tar kontakt med deg når vi har funnet en ny intervjutid.");

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute("my_page");
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/request_new_time.html.twig', array(
            'interview' => $interview,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Interview $interview
     *
     * @return Response
     */
    public function respondAction(Interview $interview)
    {
        $applicationStatus = $this->applicationManager->getApplicationStatus($interview->getApplication());

        return $this->render('interview/response.html.twig', array(
            'interview' => $interview,
            'application_status' => $applicationStatus
        ));
    }

    /**
     * @param Request $request
     * @param Interview $interview
     *
     * @return Response
     */
    public function cancelByResponseCodeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CancelInterviewConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->interviewSchedulingService->processInterviewResponse($interview, 'cancel', ['message' => $data['message']]);
            $this->addFlash('success', "Du har kansellert intervjuet ditt.");

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute("my_page");
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/response_confirm_cancel.html.twig', array(
            'interview' => $interview,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param Interview $interview
     *
     * @return RedirectResponse
     */
    public function editStatusAction(Request $request, Interview $interview)
    {
        $status = intval($request->get('status'));
        try {
            $interview->setStatus($status);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException();
        }
        $this->entityManager->flush();

        return $this->redirectToRoute(
            'interview_schedule',
            ['id' => $interview->getApplication()->getId()]
        );
    }

    public function assignCoInterviewerAction(Interview $interview)
    {
        $user = $this->getUser();

        if ($interview->getUser() === $user) {
            return $this->render('error/control_panel_error.html.twig', array(
                'error' => 'Kan ikke legge til deg selv som medintervjuer på ditt eget intervju'
            ));
        }

        if ($interview->getInterviewed()) {
            return $this->render('error/control_panel_error.html.twig', array(
                'error' => 'Kan ikke legge til deg selv som medintervjuer etter intervjuet er gjennomført'
            ));
        }

        if ($user === $interview->getInterviewer()) {
            return $this->render('error/control_panel_error.html.twig', array(
                'error' => 'Kan ikke legge til deg selv som medintervjuer når du allerede er intervjuer'
            ));
        }

        $this->interviewSchedulingService->assignCoInterviewer($interview, $user);

        return $this->redirectToRoute('applications_show_assigned');
    }

    public function adminAssignCoInterviewerAction(Request $request, Interview $interview)
    {
        $application = $interview->getApplication();
        $semester = $application->getSemester();
        $department = $application->getDepartment();
        
        $coInterviewers = $this->interviewSchedulingService->getAvailableCoInterviewers($interview);
        
        $form = $this->createForm(AddCoInterviewerType::class, null, [
            'teamUsers' => $coInterviewers
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['user'];
            $this->interviewSchedulingService->assignCoInterviewer($interview, $user);

            if ($request->get('from') === 'schedule') {
                return $this->redirectToRoute('interview_schedule', array('id' => $application->getId()));
            }

            return $this->redirectToRoute('applications_show_assigned', array(
                'department' => $department->getId(),
                'semester' => $semester->getId(),
            ));
        }

        return $this->render('interview/assign_co_interview_form.html.twig', array(
            'form' => $form->createView(),
            'interview' => $interview
        ));
    }

    public function clearCoInterviewerAction(Interview $interview)
    {
        $this->interviewSchedulingService->clearCoInterviewer($interview);

        return $this->redirectToRoute('applications_show_assigned');
    }
}
