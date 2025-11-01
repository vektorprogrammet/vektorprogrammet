<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\SchoolManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SchoolAdminController extends BaseController
{
    private $schoolManagementService;
    private $entityManager;

    /**
     * @param SchoolManagementServiceInterface $schoolManagementService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SchoolManagementServiceInterface $schoolManagementService,
        EntityManagerInterface $entityManager
    ) {
        $this->schoolManagementService = $schoolManagementService;
        $this->entityManager = $entityManager;
    }
    public function showSpecificSchoolAction(School $school)
    {
        // This prevents admins to see other departments' schools
        if (!$this->isGranted(Roles::TEAM_LEADER) &&
            !$school->belongsToDepartment($this->getUser()->getDepartment())
        ) {
            throw $this->createAccessDeniedException();
        }

        $assistantHistoryData = $this->schoolManagementService->getAssistantHistoryData($school);

        return $this->render('school_admin/specific_school.html.twig', array(
            'activeAssistantHistories' => $assistantHistoryData['activeAssistantHistories'],
            'inactiveAssistantHistories' => $assistantHistoryData['inactiveAssistantHistories'],
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
            $this->schoolManagementService->createAssistantHistory($assistantHistory, $user);

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
        $usersData = $this->schoolManagementService->getUsersData($department, $this->getUser());

        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $usersData['departments'],
            'department' => $usersData['department'],
            'users' => $usersData['users'],
        ));
    }

    public function showUsersByDepartmentAction()
    {
        $usersData = $this->schoolManagementService->getUsersData(null, $this->getUser());

        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $usersData['departments'],
            'department' => $usersData['department'],
            'users' => $usersData['users'],
        ));
    }

    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $schoolsData = $this->schoolManagementService->getSchoolsData($department);

        return $this->render('school_admin/index.html.twig', array(
            'activeSchools' => $schoolsData['activeSchools'],
            'inactiveSchools' => $schoolsData['inactiveSchools'],
            'department' => $department,
        ));
    }

    public function showSchoolsByDepartmentAction(Department $department)
    {
        $schoolsData = $this->schoolManagementService->getSchoolsData($department);

        return $this->render('school_admin/index.html.twig', array(
            'activeSchools' => $schoolsData['activeSchools'],
            'inactiveSchools' => $schoolsData['inactiveSchools'],
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
            $this->schoolManagementService->createSchoolForDepartment($school, $department);

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
            $this->schoolManagementService->deleteSchool($school);
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette skolen. ';

            return new JsonResponse($response);
        }

        return new JsonResponse($response);
    }

    public function removeUserFromSchoolAction(AssistantHistory $assistantHistory)
    {
        try {
            $this->schoolManagementService->removeUserFromSchool($assistantHistory);
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette assistent historien. ';

            return new JsonResponse($response);
        }

        return new JsonResponse($response);
    }
}
