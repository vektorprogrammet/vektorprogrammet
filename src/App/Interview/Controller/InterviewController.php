<?php

namespace App\Interview\Controller;

use App\Support\Controller\BaseController;
use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Infrastructure\Repository\TeamRepository;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Organization\Infrastructure\Entity\Team;
use App\Interview\Domain\Events\InterviewConductedEvent;
use App\Interview\Domain\Events\InterviewEvent;
use App\Interview\Form\InterviewNewTimeType;
use App\Interview\Form\AddCoInterviewerType;
use App\Interview\Form\ApplicationInterviewType;
use App\Interview\Form\CancelInterviewConfirmationType;
use App\Interview\Form\CreateInterviewType;
use App\Interview\Form\ScheduleInterviewType;
use App\Identity\Infrastructure\ReversedRoleHierarchy;
use App\Identity\Domain\Roles;
use App\Admission\Infrastructure\ApplicationManager;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * InterviewController is the controller responsible for interview actions,
 * such as showing, assigning and conducting interviews.
 */
class InterviewController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly UserRepository $userRepo,
        private readonly InterviewManager $interviewManager,
        private readonly ReversedRoleHierarchy $reversedRoleHierarchy,
        private readonly ApplicationManager $applicationManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/intervju/conduct/{id}', name: 'interview_conduct', requirements: ['id' => "\d+"], methods: ['GET', 'POST'])]
    public function conductAction(Request $request, Application $application)
    {
        if ($application->getInterview() === null) {
            throw $this->createNotFoundException();
        }
        $department = $this->getUser()->getDepartment();
        $teams = $this->teamRepo->findActiveByDepartment($department);

        if ($this->getUser() === $application->getUser()) {
            return $this->render('error/control_panel_error.html.twig', ['error' => 'Du kan ikke intervjue deg selv']);
        }

        // If the interview has not yet been conducted, create up to date answer objects for all questions in schema
        $interview = $this->interviewManager->initializeInterviewAnswers($application->getInterview());

        // Only admin and above, or the assigned interviewer, or the co interviewer should be able to conduct an interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ApplicationInterviewType::class, $application, [
            'validation_groups' => ['interview'],
            'teams' => $teams,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isNewInterview = !$interview->getInterviewed();
            $interview->setCancelled(false);

            $this->em->persist($interview);
            $this->em->flush();
            if ($isNewInterview && $form->get('saveAndSend')->isClicked()) {
                $interview->setInterviewed(true);
                $interview->setConducted(new \DateTime());
                $this->em->persist($interview);
                $this->em->flush();

                $this->eventDispatcher->dispatch(new InterviewConductedEvent($application), InterviewConductedEvent::NAME);
            }

            return $this->redirectToRoute('applications_show_interviewed', [
                'semester' => $application->getSemester()->getId(),
                'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
            ]);
        }

        return $this->render('interview/conduct.html.twig', [
            'application' => $application,
            'department' => $department,
            'teams' => $teams,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/intervju/cancel/{id}', name: 'interview_cancel', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function cancelAction(Interview $interview)
    {
        $interview->setCancelled(true);
        $this->em->persist($interview);
        $this->em->flush();

        return $this->redirectToRoute('applications_show_assigned');
    }

    /**
     * Shows the given interview.
     *
     * @return Response
     */
    #[Route('/kontrollpanel/intervju/vis/{id}', name: 'interview_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showAction(Application $application)
    {
        if (null === $interview = $application->getInterview()) {
            throw $this->createNotFoundException('Interview not found.');
        }

        // Only accessible for admin and above, or team members belonging to the same department as the interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)
            || $this->getUser() === $application->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('interview/show.html.twig', ['interview' => $interview,
            'application' => $application,
        ]);
    }

    /**
     * Deletes the given interview.
     *
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/intervju/slett/{id}', name: 'interview_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteInterviewAction(Interview $interview, Request $request)
    {
        $interview->getApplication()->setInterview(null);

        $this->em->remove($interview);
        $this->em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Deletes a bulk of interviews.
     * Takes a list of application ids through a form POST request, and deletes the interviews associated with them.
     *
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/intervju/slett/bulk', name: 'interview_delete_bulk', methods: ['POST'])]
    public function bulkDeleteInterviewAction(Request $request)
    {
        // Get the ids from the form
        $applicationIds = $request->request->get('application')['id'];

        // Get the application objects
        $applications = $this->applicationRepo->findBy(['id' => $applicationIds]);

        // Delete the interviews
        foreach ($applications as $application) {
            $interview = $application->getInterview();
            if ($interview) {
                $this->em->remove($interview);
            }
            $application->setInterview(null);
        }
        $this->em->flush();

        // AJAX response
        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Shows and handles the submission of the schedule interview form.
     * This method can also send an email to the applicant with the info from the submitted form.
     *
     * @return Response
     */
    #[Route('/kontrollpanel/intervju/settopp/{id}', name: 'interview_schedule', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
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
        $mapLink = $data['mapLink'];
        if ($form->isSubmitted()) {
            if ($mapLink && !str_starts_with((string) $mapLink, 'http')) {
                $mapLink = 'http://'.$mapLink;
            }
        }
        $invalidMapLink = $form->isSubmitted() && !empty($mapLink) && !$this->validateLink($mapLink);
        if ($invalidMapLink) {
            $this->addFlash('danger', 'Kartlinken er ikke gyldig');
        } elseif ($form->isSubmitted() && $form->isValid()) {
            if (!$interview->getResponseCode()) {
                $interview->generateAndSetResponseCode();
            }

            // Update the scheduled time for the interview
            $interview->setScheduled($data['datetime']);
            $interview->setRoom($data['room']);
            $interview->setCampus($data['campus']);

            $interview->setMapLink($mapLink);
            $interview->resetStatus();

            if ($form->get('preview')->isClicked()) {
                return $this->render('interview/preview.html.twig', [
                    'interview' => $interview,
                    'data' => $data,
                ]);
            }

            $this->em->persist($interview);
            $this->em->flush();

            // Send email if the send button was clicked
            if ($form->get('saveAndSend')->isClicked()) {
                $this->eventDispatcher->dispatch(new InterviewEvent($interview, $data), InterviewEvent::SCHEDULE);
            }

            return $this->redirectToRoute('applications_show_assigned', ['department' => $application->getDepartment()->getId(), 'semester' => $application->getSemester()->getId()]);
        }

        return $this->render('interview/schedule.html.twig', [
            'form' => $form->createView(),
            'interview' => $interview,
            'application' => $application,
        ]);
    }

    private function validateLink($link)
    {
        if (empty($link)) {
            return false;
        }

        try {
            $headers = get_headers($link);
            $statusCode = intval(explode(' ', (string) $headers[0])[1]);
        } catch (\Exception) {
            return false;
        }

        return $statusCode < 400;
    }

    /**
     * Renders and handles the submission of the assign interview form.
     * This method is used to create a new interview, or update it, and assign it to the given application.
     * It sets the interviewer and interview schema according to the form.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/intervju/fordel/{id}', name: 'interview_assign', defaults: ['id' => null], requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function assignAction(Request $request, $id = null)
    {
        if ($id === null) {
            throw $this->createNotFoundException();
        }
        $application = $this->applicationRepo->find($id);
        $user = $application->getUser();
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);

        $form = $this->createForm(CreateInterviewType::class, $application, [
            'roles' => $roles,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->getInterview()->setUser($user);
            $this->em->persist($application);
            $this->em->flush();

            return new JsonResponse(
                ['success' => true]
            );
        }

        return new JsonResponse(
            [
                'form' => $this->renderView('interview/assign_interview_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]
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
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/intervju/fordel/bulk', name: 'interview_assign_bulk', methods: ['GET', 'POST'])]
    public function bulkAssignAction(Request $request)
    {
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $form = $this->createForm(CreateInterviewType::class, null, [
            'roles' => $roles,
        ]);

        if ($request->isMethod('POST')) {
            // Get the info from the form
            $data = $request->request->all();
            // Get objects from database
            $interviewer = $this->userRepo->findOneBy(['id' => $data['interview']['interviewer']]);
            $schema = $this->em->getRepository(InterviewSchema::class)->findOneBy(['id' => $data['interview']['interviewSchema']]);
            $applications = $this->applicationRepo->findBy(['id' => $data['application']['id']]);

            // Update or create new interviews for all the given applications
            foreach ($applications as $application) {
                $this->interviewManager->assignInterviewerToApplication($interviewer, $application);

                $application->getInterview()->setInterviewSchema($schema);
                $this->em->persist($application);
            }

            $this->em->flush();

            $this->addFlash('success', 'Søknadene ble fordelt til '.$interviewer);

            return new JsonResponse([
                'success' => true,
                'request' => $request->request->all(),
            ]);
        }

        return new JsonResponse([
            'form' => $this->renderView('interview/assign_interview_form.html.twig', [
                'form' => $form->createView(),
            ]),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/intervju/aksepter/{responseCode}', name: 'interview_accept_by_response', requirements: ['responseCode' => '\w+'], methods: ['POST'])]
    public function acceptByResponseCodeAction(Interview $interview)
    {
        $interview->acceptInterview();
        $this->em->persist($interview);
        $this->em->flush();

        $formattedDate = $interview->getScheduled()->format('d. M');
        $formattedTime = $interview->getScheduled()->format('H:i');
        $room = $interview->getRoom();

        $successMessage = "Takk for at du aksepterte intervjutiden. Da sees vi $formattedDate klokka $formattedTime i $room!";
        $this->addFlash('success', $successMessage);

        if ($interview->getUser() === $this->getUser()) {
            return $this->redirectToRoute('my_page');
        }

        return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
    }

    /**
     * @return Response
     */
    #[Route('/intervju/nytid/{responseCode}', name: 'interview_request_new_time_by_response', requirements: ['responseCode' => '\w+'], methods: ['GET', 'POST'])]
    public function requestNewTimeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(InterviewNewTimeType::class, $interview, [
            'validation_groups' => ['newTimeRequest'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $interview->requestNewTime();
            $this->em->persist($interview);
            $this->em->flush();

            $this->interviewManager->sendRescheduleEmail($interview);
            $this->addFlash('success', 'Forspørsel om ny intervjutid er sendt. Vi tar kontakt med deg når vi har funnet en ny intervjutid.');

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute('my_page');
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/request_new_time.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/intervju/{responseCode}', name: 'interview_response', requirements: ['responseCode' => '\w+'], methods: ['GET'])]
    public function respondAction(Interview $interview)
    {
        $applicationStatus = $this->applicationManager->getApplicationStatus($interview->getApplication());

        return $this->render('interview/response.html.twig', [
            'interview' => $interview,
            'application_status' => $applicationStatus,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/intervju/kanseller/tilbakemelding/{responseCode}', name: 'interview_cancel_by_response', requirements: ['responseCode' => '\w+'], methods: ['GET', 'POST'])]
    public function cancelByResponseCodeAction(Request $request, Interview $interview)
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CancelInterviewConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $interview->setCancelMessage($data['message']);
            $interview->cancel();
            $this->em->persist($interview);
            $this->em->flush();

            $this->interviewManager->sendCancelEmail($interview);
            $this->addFlash('success', 'Du har kansellert intervjuet ditt.');

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute('my_page');
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/response_confirm_cancel.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/intervju/status/{id}', name: 'interview_edit_status', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editStatusAction(Request $request, Interview $interview)
    {
        $status = intval($request->get('status'));
        try {
            $interview->setStatus($status);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException();
        }
        $this->em->flush();

        return $this->redirectToRoute(
            'interview_schedule',
            ['id' => $interview->getApplication()->getId()]
        );
    }

    #[Route('/kontrollpanel/intervju/assign_co_interviewer/{id}', name: 'interview_assign_co_interviewer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function assignCoInterviewerAction(Interview $interview)
    {
        if ($interview->getUser() === $this->getUser()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer på ditt eget intervju',
            ]);
        }

        if ($interview->getInterviewed()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer etter intervjuet er gjennomført',
            ]);
        }

        if ($this->getUser() === $interview->getInterviewer()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer når du allerede er intervjuer',
            ]);
        }

        $interview->setCoInterviewer($this->getUser());
        $this->em->persist($interview);
        $this->em->flush();
        $this->eventDispatcher->dispatch(new InterviewEvent($interview), InterviewEvent::COASSIGN);

        return $this->redirectToRoute('applications_show_assigned');
    }

    #[Route('/kontrollpanel/intervju/admin_assign_co_interviewer/{id}', name: 'interview_admin_assign_co_interviewer', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function adminAssignCoInterviewerAction(Request $request, Interview $interview)
    {
        $semester = $interview->getApplication()->getSemester();
        $department = $interview->getApplication()->getDepartment();
        $teamUsers = $this->userRepo
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);
        $coInterviewers = array_merge(array_diff($teamUsers, [$interview->getInterviewer(), $interview->getCoInterviewer()]));
        $form = $this->createForm(AddCoInterviewerType::class, null, [
            'teamUsers' => $coInterviewers,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['user'];
            $interview->setCoInterviewer($user);
            $this->em->persist($interview);
            $this->em->flush();

            if ($request->get('from') === 'schedule') {
                return $this->redirectToRoute('interview_schedule', ['id' => $interview->getApplication()->getId()]);
            }

            return $this->redirectToRoute('applications_show_assigned', [
                'department' => $department->getId(),
                'semester' => $semester->getId(),
            ]);
        }

        return $this->render('interview/assign_co_interview_form.html.twig', [
            'form' => $form->createView(),
            'interview' => $interview,
        ]);
    }

    #[Route('/kontrollpanel/intervju/interview_clear_co_interviewer/{id}', name: 'interview_clear_co_interviewer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function clearCoInterviewerAction(Interview $interview)
    {
        $interview->setCoInterviewer(null);
        $this->em->persist($interview);
        $this->em->flush();

        return $this->redirectToRoute('applications_show_assigned');
    }
}
