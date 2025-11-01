<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use AppBundle\Entity\User;
use AppBundle\Event\AssistantHistoryCreatedEvent;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\SchoolRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SchoolAdminController extends BaseController
{
    private $assistantHistoryRepository;
    private $departmentRepository;
    private $userRepository;
    private $schoolRepository;
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param SchoolRepositoryInterface $schoolRepository
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository,
        SchoolRepositoryInterface $schoolRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->schoolRepository = $schoolRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }
    public function showSpecificSchoolAction(School $school)
    {
        // This prevents admins to see other departments' schools
        if (!$this->isGranted(Roles::TEAM_LEADER) &&
            !$school->belongsToDepartment($this->getUser()->getDepartment())
        ) {
            throw $this->createAccessDeniedException();
        }

        $inactiveAssistantHistories = $this->assistantHistoryRepository->findInactiveAssistantHistoriesBySchool($school);
        $activeAssistantHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesBySchool($school);

        return $this->render('school_admin/specific_school.html.twig', array(
            'activeAssistantHistories' => $activeAssistantHistories,
            'inactiveAssistantHistories' => $inactiveAssistantHistories,
            'school' => $school,
        ));
    }

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
            'department' => $department
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assistantHistory->setUser($user);
            $this->entityManager->persist($assistantHistory);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(AssistantHistoryCreatedEvent::NAME, new AssistantHistoryCreatedEvent($assistantHistory));

            return $this->redirect($this->generateUrl('schooladmin_show_users_of_department'));
        }

        // Return the form view
        return $this->render('school_admin/create_assistant_history.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    public function showUsersByDepartmentSuperadminAction(Department $department)
    {
        $activeDepartments = $this->departmentRepository->findActive();

        $users = $this->userRepository->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $activeDepartments,
            'department' => $department,
            'users' => $users,
        ));
    }

    public function showUsersByDepartmentAction()
    {
        $user = $this->getUser();

        // Finds all the departments
        $activeDepartments = $this->departmentRepository->findActive();

        // Find the department of the user
        $department = $user->getFieldOfStudy()->getDepartment();

        // Find all the users of the department that are active
        $users = $this->userRepository->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $activeDepartments,
            'department' => $department,
            'users' => $users,
        ));
    }

    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Find schools that are connected to the department of the user
        $activeSchools = $this->schoolRepository->findActiveSchoolsByDepartment($department);

        $inactiveSchools = $this->schoolRepository->findInactiveSchoolsByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/index.html.twig', array(
            'activeSchools' => $activeSchools,
            'inactiveSchools' => $inactiveSchools,
            'department' => $department,
        ));
    }

    public function showSchoolsByDepartmentAction(Department $department)
    {
        // Finds the schools for the given department
        $activeSchools = $this->schoolRepository->findActiveSchoolsByDepartment($department);
        $inactiveSchools = $this->schoolRepository->findInactiveSchoolsByDepartment($department);

        // Renders the view with the variables
        return $this->render('school_admin/index.html.twig', array(
            'activeSchools' => $activeSchools,
            'inactiveSchools' => $inactiveSchools,
            'department' => $department,
        ));
    }

    public function updateSchoolAction(Request $request, School $school)
    {
        // Create the formType
        $form = $this->createForm(CreateSchoolType::class, $school);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($school);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('schooladmin_show'));
        }

        // Return the form view
        return $this->render('school_admin/create_school.html.twig', array(
            'form' => $form->createView(),
            'school' => $school
        ));
    }

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
            $this->entityManager->persist($school);
            $this->entityManager->persist($department);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('schooladmin_show'));
        }

        // Render the view
        return $this->render('school_admin/create_school.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteSchoolByIdAction(School $school)
    {
        try {
            // This deletes the given school
            $this->entityManager->remove($school);
            $this->entityManager->flush();

            // a response back to AJAX
            $response['success'] = true;
        } catch (Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette skolen. ';

            return new JsonResponse($response);
        }
        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function removeUserFromSchoolAction(AssistantHistory $assistantHistory)
    {
        try {
            // This deletes the assistant history
            $this->entityManager->remove($assistantHistory);
            $this->entityManager->flush();

            // a response back to AJAX
            $response['success'] = true;
        } catch (Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette assistent historien. ';

            return new JsonResponse($response);
        }
        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
