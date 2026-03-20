<?php

namespace App\Admission\Controller;

use App\Admission\Domain\Events\ApplicationCreatedEvent;
use App\Admission\Form\ApplicationType;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Interview\Domain\Rules\InterviewCounter;
use App\Interview\Infrastructure\InterviewDistributionFactory;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamInterest;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Support\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly UserRepository $userRepo,
        private readonly InterviewCounter $interviewCounter,
        private readonly InterviewDistributionFactory $interviewDistributionFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @return Response
     */
    #[Route('/kontrollpanel/opptak', name: 'admissionadmin_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        return $this->showNewApplicationsAction($request);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/opptak/nye', name: 'applications_show_new', defaults: ['department' => null, 'semester' => null], methods: ['GET'])]
    public function showNewApplicationsAction(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $admissionPeriod = $this->admissionPeriodRepo
                ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepo
                ->findNewApplicationsByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('admission_admin/new_applications_table.html.twig', [
            'applications' => $applications,
            'semester' => $semester,
            'department' => $department,
            'status' => 'new',
        ]);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/opptak/fordelt', name: 'applications_show_assigned', methods: ['GET'])]
    public function showAssignedApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        $interviewDistributions = [];
        $cancelledApplications = [];
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepo->findAssignedApplicants($admissionPeriod);
            $interviewDistributions = $this->interviewDistributionFactory
                ->createInterviewDistributions($applications, $admissionPeriod);
            $cancelledApplications = $this->applicationRepo->findCancelledApplicants($admissionPeriod);
            $applicationsAssignedToUser = $this->applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('admission_admin/assigned_applications_table.html.twig', [
            'status' => 'assigned',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ]);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/opptak/intervjuet', name: 'applications_show_interviewed', methods: ['GET'])]
    public function showInterviewedApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepo
                ->findInterviewedApplicants($admissionPeriod);
        }

        $interviews = array_filter(array_map(fn ($app) => $app->getInterview(), $applications));

        return $this->render('admission_admin/interviewed_applications_table.html.twig', [
            'status' => 'interviewed',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'yes' => $this->interviewCounter->count($interviews, InterviewCounter::YES),
            'no' => $this->interviewCounter->count($interviews, InterviewCounter::NO),
            'maybe' => $this->interviewCounter->count($interviews, InterviewCounter::MAYBE),
        ]);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/opptak/gamle', name: 'applications_show_existing', methods: ['GET'])]
    public function showExistingApplicationsAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }
        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->applicationRepo
                ->findExistingApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', [
            'status' => 'existing',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
        ]);
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/opptakadmin/slett/{id}', name: 'admissionadmin_delete_application_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteApplicationByIdAction(Application $application)
    {
        $this->em->remove($application);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/application/existing/delete/{id}', name: 'delete_application_existing_user')]
    public function deleteApplicationExistingAssistantAction(Application $application)
    {
        $this->em->remove($application);
        $this->em->flush();

        $this->addFlash('success', 'Søknaden ble slettet.');

        return $this->redirectToRoute('applications_show_existing', [
            'department' => $application->getDepartment(),
            'semester' => $application->getSemester()->getId(),
        ]);
    }

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/opptakadmin/slett/bulk', name: 'admissionadmin_delete_application_bulk', methods: ['POST'])]
    public function bulkDeleteApplicationAction(Request $request)
    {
        // Get the ids from the form
        $applicationIds = array_map(intval(...), $request->request->get('application')['id']);

        // Delete the applications
        foreach ($applicationIds as $id) {
            $application = $this->applicationRepo->find($id);

            if ($application !== null) {
                $this->em->remove($application);
            }
        }

        $this->em->flush();

        $this->addFlash('success', 'Søknadene ble slettet.');

        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/kontrollpanel/opprettsoker', name: 'register_applicant', methods: ['GET', 'POST'])]
    public function createApplicationAction(Request $request)
    {
        $department = $this->getUser()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $currentSemester);
        if ($admissionPeriod === null) {
            throw new BadRequestHttpException();
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application, [
            'departmentId' => $department->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepo->findOneBy(['email' => $application->getUser()->getEmail()]);
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setAdmissionPeriod($admissionPeriod);
            $this->em->persist($application);
            $this->em->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('register_applicant', ['id' => $department->getId()]);
        }

        return $this->render('admission_admin/create_application.html.twig', [
            'department' => $department,
            'semester' => $currentSemester,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/opptakadmin/soknad/{id}', name: 'admissionadmin_show_application', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showApplicationAction(Application $application)
    {
        if (!$application->getPreviousParticipation()) {
            throw $this->createNotFoundException('Søknaden finnes ikke');
        }

        return $this->render('admission_admin/application.html.twig', [
            'application' => $application,
        ]);
    }

    /**
     * @return Response|null
     */
    #[Route('/kontrollpanel/opptakadmin/teaminteresse', name: 'admissionadmin_team_interest', methods: ['GET'])]
    public function showTeamInterestAction(Request $request)
    {
        $user = $this->getUser();
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationsWithTeamInterest = [];
        $teams = [];
        if ($admissionPeriod !== null) {
            $applicationsWithTeamInterest = $this->applicationRepo
                ->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
            $teams = $this->em->getRepository(Team::class)->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        }

        $possibleApplicants = $this->em
            ->getRepository(TeamInterest::class)
            ->findBy(['semester' => $semester, 'department' => $department]);

        return $this->render('admission_admin/teamInterest.html.twig', [
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
        ]);
    }
}
