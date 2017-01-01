<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\School;
use AppBundle\Form\Type\CreateSchoolType;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Form\Type\CreateAssistantHistoryType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        // Find department of the user
        $department = $user->getFieldOfStudy()->getDepartment();

        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        if (!($this->isGranted('ROLE_SUPER_ADMIN') || ($this->isGranted('ROLE_ADMIN') && $department == $this->getUser()->getFieldOfStudy()->getDepartment()))) {
            throw new AccessDeniedException();
        }

        // A new assistant history entity
        $assistantHistory = new AssistantHistory();

        // Create the formType
        $form = $this->createForm(new CreateAssistantHistoryType($department), $assistantHistory);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isValid()) {

            // Set the user of the assistant history
            $assistantHistory->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($assistantHistory);

            // Check if user already has user name and password
            if ($user->getUserName() !== null && $user->getPassword() !== null) {
                $user->setActive(true);
            } else { // Send new user code for user to create user name and password

                // Send new user code only if assistant history is added to current semester
                if ($assistantHistory->getSemester() === $currentSemester && $user->getNewUserCode() === null) {
                    $userRegistration = $this->get('app.user.registration');
                    $newUserCode = $userRegistration->setNewUserCode($user);

                    $em->persist($user);

                    $emailMessage = $userRegistration->createActivationEmail($user, $newUserCode);
                    $this->get('mailer')->send($emailMessage);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('schooladmin_show_users_of_department'));
        }

        // Return the form view
        return $this->render('school_admin/create_assistant_history.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showUsersByDepartmentSuperadminAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Find the ID variable sent by the request
            $id = $request->get('id');

            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Find the department with the ID sent by the request
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);

            // Find all the users of the department that are active
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

            // Return the view with suitable variables
            return $this->render('school_admin/all_users.html.twig', array(
                'departments' => $allDepartments,
                'users' => $users,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showUsersByDepartmentAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            // Get the current user
            $user = $this->get('security.context')->getToken()->getUser();

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
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
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
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showSchoolsByDepartmentAction(request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Find the ID variable sent by the request
            $id = $request->get('id');

            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Find the department with the ID sent by the request
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneById($id);

            // Finds the schools for the given department
            $schools = $this->getDoctrine()->getRepository('AppBundle:School')->findSchoolsByDepartment($department);

            // Renders the view with the variables
            return $this->render('school_admin/index.html.twig', array(
                'departments' => $allDepartments,
                'schools' => $schools,
                'departmentName' => $department->getShortName(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function updateSchoolAction(request $request)
    {

        // If it is a superadmin they can edit schools
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Get the ID variable from the request
            $id = $request->get('id');

            // New school entity
            $school = new School();

            // Set the new school entity to a school entity with the ID from the request
            $em = $this->getDoctrine()->getManager();
            $school = $em->getRepository('AppBundle:School')->find($id);

            // Create the formType
            $form = $this->createForm(new CreateSchoolType(), $school);

            // Handle the form
            $form->handleRequest($request);

            // Check if the form is valid
            if ($form->isValid()) {
                $em->persist($school);
                $em->flush();

                return $this->redirect($this->generateUrl('schooladmin_show'));
            }

            // Return the form view
            return $this->render('school_admin/create_school.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            // If access denied return view
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function createSchoolForDepartmentAction(request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Get the ID variable sent by request
            $id = $request->get('id');

            // Find the department with a ID equal to the ID variable sent by the request
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneById($id);

            // New school entity
            $school = new School();

            // Create the form
            $form = $this->createForm(new CreateSchoolType(), $school);

            // Handle the form
            $form->handleRequest($request);

            // The fields of the form is checked if they contain the correct information
            if ($form->isValid()) {
                // Since it is a bidirectional relationship we have to add school to department and department to school
                // Set the school of the department
                $department->addSchool($school);
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
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function deleteSchoolByIdAction(Request $request)
    {

        // Get the ID variable sent by the request
        $id = $request->get('id');

        try {

            // Only the SUPER_ADMIN role can delete schools
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given school
                $em = $this->getDoctrine()->getEntityManager();
                $school = $this->getDoctrine()->getRepository('AppBundle:School')->find($id);
                $em->remove($school);
                $em->flush();

                // a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = $e->getMessage();

            return new JsonResponse($response);
        }
        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function removeUserFromSchoolByIdAction(Request $request)
    {

        // Get the ID variable sent by the request
        $id = $request->get('id');

        try {

            // Only the SUPER_ADMIN role can delete schools
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the assistant history
                $em = $this->getDoctrine()->getEntityManager();
                $assistantHistory = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->find($id);
                $em->remove($assistantHistory);
                $em->flush();

                // a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
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
