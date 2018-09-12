<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\ApplicationRepository;
use AppBundle\Entity\User;
use AppBundle\Event\AssistantHistoryCreatedEvent;
use AppBundle\Form\Type\AssistantDelegationInfoType;
use AppBundle\Model\AssistantDelegationInfo;
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
        // TODO: Superadmins should be able to switch and semester
        $semester = $department->getCurrentOrLatestSemester();

        $info = new AssistantDelegationInfo();
        $info->setUser($user);
        $form = $this->createForm(new AssistantDelegationInfoType($department), $info);

        $form->handleRequest($request);

        if($form->isValid()) {
            $assistantPosition = new AssistantHistory();
            $assistantPosition->setUser($user);
            $assistantPosition->setSemester($semester);

            $em = $this->getDoctrine()->getManager();
            $em->persist($assistantPosition);
            $em->flush();

            $this->get('app.work_day.manager')->createAndPersistWorkDays($info, $assistantPosition);
        }

        // Deny access if not super admin and trying to delegate user in other department
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('school_admin/create_assistant_position.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showUsersByDepartmentSuperadminAction(Department $department)
    {
        $activeDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/all_users.html.twig', array(
            'departments' => $activeDepartments,
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
            'users' => $users,
        ));
    }

    public function showAction()
    {
        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Find schools that are connected to the department of the user
        $schools = $this->getDoctrine()->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

        // Return the view with suitable variables
        return $this->render('school_admin/index.html.twig', array(
            'schools' => $schools,
            'department' => $department,
        ));
    }

    public function showSchoolsByDepartmentAction(Department $department)
    {
        // Finds the schools for the given department
        $schools = $this->getDoctrine()->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

        // Renders the view with the variables
        return $this->render('school_admin/index.html.twig', array(
            'schools' => $schools,
            'department' => $department,
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
