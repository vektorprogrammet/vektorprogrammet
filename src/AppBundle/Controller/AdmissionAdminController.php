<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamInterest;
use AppBundle\Entity\User;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Repository\Contract\TeamRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\InterviewCounterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends BaseController
{
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $teamRepository;
    private $interviewCounter;
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param TeamRepositoryInterface $teamRepository
     * @param InterviewCounterInterface $interviewCounter
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        TeamRepositoryInterface $teamRepository,
        InterviewCounterInterface $interviewCounter,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->teamRepository = $teamRepository;
        $this->interviewCounter = $interviewCounter;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        return $this->showNewApplicationsAction($request);
    }


    /**
     * @param Request $request
     * @return Response|null
     */
    public function showNewApplicationsAction(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepository->findNewApplicationsByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('admission_admin/new_applications_table.html.twig', array(
            'applications' => $applications,
            'semester' => $semester,
            'department' => $department,
            'status' => 'new',
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showAssignedApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        $interviewDistributions = [];
        $cancelledApplications = [];
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepository->findAssignedApplicants($admissionPeriod);
            $interviewDistributions = $this->interviewCounter->createInterviewDistributions($applications, $admissionPeriod);
            $cancelledApplications = $this->applicationRepository->findCancelledApplicants($admissionPeriod);
            $applicationsAssignedToUser = $this->applicationRepository->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('admission_admin/assigned_applications_table.html.twig', array(
            'status' => 'assigned',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showInterviewedApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepository->findInterviewedApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/interviewed_applications_table.html.twig', array(
            'status' => 'interviewed',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'yes' => $this->interviewCounter->count($applications, InterviewCounter::YES),
            'no' => $this->interviewCounter->count($applications, InterviewCounter::NO),
            'maybe' => $this->interviewCounter->count($applications, InterviewCounter::MAYBE),
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showExistingApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }
        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepository->findExistingApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', array(
            'status' => 'existing',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
        ));
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @param Application $application
     *
     * @return JsonResponse
     */
    public function deleteApplicationByIdAction(Application $application)
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @Route("/kontrollpanel/application/existing/delete/{id}", name="delete_application_existing_user")
     * @param Application $application
     *
     * @return RedirectResponse
     */
    public function deleteApplicationExistingAssistantAction(Application $application)
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();

        $this->addFlash('success', 'Søknaden ble slettet.');

        return $this->redirectToRoute('applications_show_existing', array(
            'department' => $application->getDepartment(),
            'semester' => $application->getSemester()->getId()
        ));
    }

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteApplicationAction(Request $request)
    {
        // Get the ids from the form
        $applicationIds = array_map('intval', $request->request->get('application')['id']);

        // Delete the applications
        foreach ($applicationIds as $id) {
            $application = $this->entityManager->getRepository(Application::class)->find($id);

            if ($application !== null) {
                $this->entityManager->remove($application);
            }
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Søknadene ble slettet.');

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function createApplicationAction(Request $request)
    {
        $department = $this->getUser()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $currentSemester);
        if ($admissionPeriod === null) {
            throw new BadRequestHttpException();
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application, array(
            'departmentId' => $department->getId(),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(array('email' => $application->getUser()->getEmail()));
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setAdmissionPeriod($admissionPeriod);
            $this->entityManager->persist($application);
            $this->entityManager->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->eventDispatcher->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            return $this->redirectToRoute('register_applicant', array('id' => $department->getId()));
        }

        return $this->render(':admission_admin:create_application.html.twig', array(
            'department' => $department,
            'semester' => $currentSemester,
            'form' => $form->createView(),
        ));
    }

    public function showApplicationAction(Application $application)
    {
        if (!$application->getPreviousParticipation()) {
            throw $this->createNotFoundException('Søknaden finnes ikke');
        }

        return $this->render('admission_admin/application.html.twig', array(
            'application' => $application,
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showTeamInterestAction(Request $request)
    {
        $user = $this->getUser();
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationsWithTeamInterest = [];
        $teams = [];
        if ($admissionPeriod !== null) {
            $applicationsWithTeamInterest = $this->applicationRepository->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
            $teams = $this->teamRepository->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        }

        $possibleApplicants = $this->entityManager->getRepository(TeamInterest::class)
            ->findBy(array('semester' => $semester, 'department' => $department));

        return $this->render('admission_admin/teamInterest.html.twig', array(
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
        ));
    }
}
