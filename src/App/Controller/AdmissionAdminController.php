<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamInterest;
use App\Entity\User;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationType;
use App\Role\Roles;
use App\Service\InterviewCounter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Attribute\Route;
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
    public function __construct(
        private AdmissionPeriodRepository $admissionPeriodRepo,
        private ApplicationRepository $applicationRepo,
        private UserRepository $userRepo,
        private InterviewCounter $interviewCounter,
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
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
            $interviewDistributions = $this->interviewCounter
                ->createInterviewDistributions($applications, $admissionPeriod);
            $cancelledApplications = $this->applicationRepo->findCancelledApplicants($admissionPeriod);
            $applicationsAssignedToUser = $this->applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
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
        $this->em->remove($application);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @param Application $application
     *
     * @return RedirectResponse
     */
    #[Route("/kontrollpanel/application/existing/delete/{id}", name: "delete_application_existing_user")]
    public function deleteApplicationExistingAssistantAction(Application $application)
    {
        $this->em->remove($application);
        $this->em->flush();

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
        $form = $this->createForm(ApplicationType::class, $application, array(
            'departmentId' => $department->getId(),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepo->findOneBy(array('email' => $application->getUser()->getEmail()));
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setAdmissionPeriod($admissionPeriod);
            $this->em->persist($application);
            $this->em->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('register_applicant', array('id' => $department->getId()));
        }

        return $this->render('admission_admin/create_application.html.twig', array(
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
