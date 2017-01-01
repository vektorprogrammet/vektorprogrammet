<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Event\AssistantHistoryCreatedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\School;
use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use Symfony\Component\HttpFoundation\JsonResponse;

class SchoolAdminController extends Controller
{
    public function showSpecificSchoolAction(School $school)
    {
        // This prevents admins to see other departments' schools
        if (!$this->isGranted('ROLE_SUPER_ADMIN') &&
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
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        $assistantHistory = new AssistantHistory();

        $form = $this->createForm(new CreateAssistantHistoryType($department), $assistantHistory);

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
        ));
    }

    public function showUsersByDepartmentSuperadminAction(Department $department)
    {
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $allDepartments,
            'users' => $users,
        ));
    }

    public function showUsersByDepartmentAction()
    {
        $user = $this->getUser();

        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Find the department of the user
        $department = $user->getFieldOfStudy()->getDepartment();

        // Find all the users of the department that are active
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $allDepartments,
            'users' => $users,
        ));
    }

    public function showAction()
    {
        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the department for the current logged in user
        $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();

        // Find schools that are connected to the department of the user
        $schools = $this->getDoctrine()->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/index.html.twig', array(
            'departments' => $allDepartments,
            'schools' => $schools,
            'departmentName' => $department->getShortName(),
        ));
    }

    public function showSchoolsByDepartmentAction(Department $department)
    {
        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the schools for the given department
        $schools = $this->getDoctrine()->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

        // Renders the view with the variables
        return $this->render('school_admin/index.html.twig', array(
            'departments' => $allDepartments,
            'schools' => $schools,
            'departmentName' => $department->getShortName(),
        ));
    }

    public function updateSchoolAction(Request $request, School $school)
    {
        // Create the formType
        $form = $this->createForm(new CreateSchoolType(), $school);

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
        ));
    }

    public function createSchoolForDepartmentAction(Request $request, Department $department)
    {
        $school = new School();

        $form = $this->createForm(new CreateSchoolType(), $school);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Set the department of the school
            $school->addDepartment($department);
            // If valid insert into database
            $em = $this->getDoctrine()->getManager();
            $em->persist($school);
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette assistent historien. ';

            return new JsonResponse($response);
        }
        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
