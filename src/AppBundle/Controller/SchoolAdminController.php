<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Event\AssistantHistoryCreatedEvent;
use AppBundle\Role\Roles;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\School;
use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use Symfony\Component\HttpFoundation\JsonResponse;

class SchoolAdminController extends BaseController
{
    public function showSpecificSchoolAction(School $school)
    {
        // This prevents admins to see other departments' schools
        if (!$this->isGranted(Roles::TEAM_LEADER) &&
            !$school->belongsToDepartment($this->getUser()->getDepartment())
        ) {
            throw $this->createAccessDeniedException();
        }

        $inactiveAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findInactiveAssistantHistoriesBySchool($school);
        $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($school);

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

        if ($form->isValid()) {
            $assistantHistory->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($assistantHistory);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(AssistantHistoryCreatedEvent::NAME, new AssistantHistoryCreatedEvent($assistantHistory));

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
        $activeDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

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
        $activeDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        // Find the department of the user
        $department = $user->getFieldOfStudy()->getDepartment();

        // Find all the users of the department that are active
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

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
        $activeSchools = $this->getDoctrine()->getRepository('AppBundle:School')->findActiveSchoolsByDepartment($department);

        $inactiveSchools = $this->getDoctrine()->getRepository('AppBundle:School')->findInactiveSchoolsByDepartment($department);

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
        $activeSchools = $this->getDoctrine()->getRepository('AppBundle:School')->findActiveSchoolsByDepartment($department);
        $inactiveSchools = $this->getDoctrine()->getRepository('AppBundle:School')->findInactiveSchoolsByDepartment($department);

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
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($school);
            $em->flush();

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

        if ($form->isValid()) {
            // Set the department of the school
            $school->addDepartment($department);
            $department->addSchool($school);
            // If valid insert into database
            $em = $this->getDoctrine()->getManager();
            $em->persist($school);
            $em->persist($department);
            $em->flush();

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
            $em = $this->getDoctrine()->getManager();
            $em->remove($school);
            $em->flush();

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
            $em = $this->getDoctrine()->getManager();
            $em->remove($assistantHistory);
            $em->flush();

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
