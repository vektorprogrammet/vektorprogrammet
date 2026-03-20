<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\AssistantHistory;
use App\Entity\Department;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SchoolRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\School;
use App\Entity\User;
use App\Event\AssistantHistoryCreatedEvent;
use App\Form\Type\CreateAssistantHistoryType;
use App\Form\Type\CreateSchoolType;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SchoolAdminController extends BaseController
{
    public function __construct(
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly UserRepository $userRepo,
        private readonly SchoolRepository $schoolRepo,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/kontrollpanel/skole/{id}', name: 'schooladmin_show_specific_school', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showSpecificSchoolAction(School $school)
    {
        // This prevents admins to see other departments' schools
        if (!$this->isGranted(Roles::TEAM_LEADER)
            && !$school->belongsToDepartment($this->getUser()->getDepartment())
        ) {
            throw $this->createAccessDeniedException();
        }

        $inactiveAssistantHistories = $this->assistantHistoryRepo->findInactiveAssistantHistoriesBySchool($school);
        $activeAssistantHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesBySchool($school);

        return $this->render('school_admin/specific_school.html.twig', [
            'activeAssistantHistories' => $activeAssistantHistories,
            'inactiveAssistantHistories' => $inactiveAssistantHistories,
            'school' => $school,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/tildel/skole/{id}', name: 'schooladmin_delegate_school_to_user', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delegateSchoolToUserAction(Request $request, User $user)
    {
        $department = $user->getDepartment();

        // Deny access if not super admin and trying to delegate user in other department
        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        $assistantHistory = new AssistantHistory();
        $assistantHistory->setDepartment($department);
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assistantHistory->setUser($user);
            $this->em->persist($assistantHistory);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new AssistantHistoryCreatedEvent($assistantHistory), AssistantHistoryCreatedEvent::NAME);

            return $this->redirect($this->generateUrl('schooladmin_show_users_of_department'));
        }

        // Return the form view
        return $this->render('school_admin/create_assistant_history.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/brukere/avdeling/{id}', name: 'schooladmin_show_users_of_department_superadmin', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showUsersByDepartmentSuperadminAction(Department $department)
    {
        $activeDepartments = $this->departmentRepo->findActive();

        $users = $this->userRepo->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', [
            'departments' => $activeDepartments,
            'department' => $department,
            'users' => $users,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/brukere', name: 'schooladmin_show_users_of_department', methods: ['GET'])]
    public function showUsersByDepartmentAction()
    {
        $user = $this->getUser();

        // Finds all the departments
        $activeDepartments = $this->departmentRepo->findActive();

        // Find the department of the user
        $department = $user->getFieldOfStudy()->getDepartment();

        // Find all the users of the department that are active
        $users = $this->userRepo->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', [
            'departments' => $activeDepartments,
            'department' => $department,
            'users' => $users,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin', name: 'schooladmin_show', methods: ['GET'])]
    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Find schools that are connected to the department of the user
        $activeSchools = $this->schoolRepo->findActiveSchoolsByDepartment($department);

        $inactiveSchools = $this->schoolRepo->findInactiveSchoolsByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/index.html.twig', [
            'activeSchools' => $activeSchools,
            'inactiveSchools' => $inactiveSchools,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/avdeling/{id}', name: 'schooladmin_filter_schools_by_department', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showSchoolsByDepartmentAction(Department $department)
    {
        // Finds the schools for the given department
        $activeSchools = $this->schoolRepo->findActiveSchoolsByDepartment($department);
        $inactiveSchools = $this->schoolRepo->findInactiveSchoolsByDepartment($department);

        // Renders the view with the variables
        return $this->render('school_admin/index.html.twig', [
            'activeSchools' => $activeSchools,
            'inactiveSchools' => $inactiveSchools,
            'department' => $department,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/oppdater/{id}', name: 'schooladmin_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateSchoolAction(Request $request, School $school)
    {
        // Create the formType
        $form = $this->createForm(CreateSchoolType::class, $school);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($school);
            $this->em->flush();

            return $this->redirect($this->generateUrl('schooladmin_show'));
        }

        // Return the form view
        return $this->render('school_admin/create_school.html.twig', [
            'form' => $form->createView(),
            'school' => $school,
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/opprett/{id}', name: 'schooladmin_create_school_by_department', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createSchoolForDepartmentAction(Request $request, Department $department)
    {
        $school = new School();

        $form = $this->createForm(CreateSchoolType::class, $school);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the department of the school
            $school->addDepartment($department);
            $department->addSchool($school);
            // If valid insert into database
            $this->em->persist($school);
            $this->em->persist($department);
            $this->em->flush();

            return $this->redirect($this->generateUrl('schooladmin_show'));
        }

        // Render the view
        return $this->render('school_admin/create_school.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kontrollpanel/skoleadmin/slett/{id}', name: 'schooladmin_delete_school_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteSchoolByIdAction(School $school)
    {
        try {
            // This deletes the given school
            $this->em->remove($school);
            $this->em->flush();

            // a response back to AJAX
            $response['success'] = true;
        } catch (\Exception) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette skolen. ';

            return new JsonResponse($response);
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    #[Route('/kontrollpanel/skoleadmin/historikk/slett/{id}', name: 'schooladmin_remove_user_from_school_by_id', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function removeUserFromSchoolAction(AssistantHistory $assistantHistory)
    {
        try {
            // This deletes the assistant history
            $this->em->remove($assistantHistory);
            $this->em->flush();

            // a response back to AJAX
            $response['success'] = true;
        } catch (\Exception) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette assistent historien. ';

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
