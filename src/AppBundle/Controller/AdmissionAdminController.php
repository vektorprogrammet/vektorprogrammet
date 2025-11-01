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
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\AdmissionAdminServiceInterface;
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
    private $admissionAdminService;
    private $userRepository;
    private $entityManager;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AdmissionAdminServiceInterface $admissionAdminService
     * @param UserRepositoryInterface $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        AdmissionAdminServiceInterface $admissionAdminService,
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->admissionAdminService = $admissionAdminService;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
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
            $applications = $this->admissionAdminService->getNewApplicationsData($admissionPeriod);
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

        $assignedData = [];
        if ($admissionPeriod !== null) {
            $assignedData = $this->admissionAdminService->getAssignedApplicationsData($admissionPeriod, $this->getUser());
        }

        return $this->render('admission_admin/assigned_applications_table.html.twig', array(
            'status' => 'assigned',
            'applications' => $assignedData['applications'] ?? [],
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $assignedData['interviewDistributions'] ?? [],
            'cancelledApplications' => $assignedData['cancelledApplications'] ?? [],
            'yourApplications' => $assignedData['yourApplications'] ?? [],
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

        $interviewedData = [];
        if ($admissionPeriod !== null) {
            $interviewedData = $this->admissionAdminService->getInterviewedApplicationsData($admissionPeriod);
        }

        return $this->render('admission_admin/interviewed_applications_table.html.twig', array(
            'status' => 'interviewed',
            'applications' => $interviewedData['applications'] ?? [],
            'department' => $department,
            'semester' => $semester,
            'yes' => $interviewedData['yes'] ?? 0,
            'no' => $interviewedData['no'] ?? 0,
            'maybe' => $interviewedData['maybe'] ?? 0,
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
            $applications = $this->admissionAdminService->getExistingApplicationsData($admissionPeriod);
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

        $this->admissionAdminService->bulkDeleteApplications($applicationIds);

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
            $user = $this->userRepository->findOneBy(array('email' => $application->getUser()->getEmail()));
            
            $this->admissionAdminService->createApplication($application, $admissionPeriod, $user);

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

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

        $teamInterestData = [];
        if ($admissionPeriod !== null) {
            $teamInterestData = $this->admissionAdminService->getTeamInterestData($admissionPeriod, $semester, $department);
        }

        return $this->render('admission_admin/teamInterest.html.twig', array(
            'applicationsWithTeamInterest' => $teamInterestData['applicationsWithTeamInterest'] ?? [],
            'possibleApplicants' => $teamInterestData['possibleApplicants'] ?? [],
            'department' => $department,
            'semester' => $semester,
            'teams' => $teamInterestData['teams'] ?? [],
        ));
    }
}
