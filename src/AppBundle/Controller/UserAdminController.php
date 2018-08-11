<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CreateUserType;
use AppBundle\Form\Type\NewUserType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAdminController extends Controller
{
    public function createUserAction(Request $request, Department $department = null)
    {
        if (!$this->isGranted(Roles::TEAM_LEADER) || $department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Create the user object
        $user = new User();

        $form = $this->createForm(CreateUserType::class, $user, array(
            'validation_groups' => array('create_user'),
            'department' => $department
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('app.user.registration')->sendActivationCode($user);

            return $this->redirectToRoute('useradmin_show');
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }

    public function showAction()
    {
        // Finds all the departments
        $activeDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $activeUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllInActiveUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ));
    }

    public function showUsersByDepartmentAction(Department $department)
    {
        // Finds all the departments
        $activeDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();

        $activeUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllInActiveUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ));
    }

    public function deleteUserByIdAction(User $user)
    {
        if ($this->isGranted(ROLES::ADMIN) || $user->getDepartment() == $this->getUser()->getDepartment()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        } else {
            throw $this->createAccessDeniedException();
        }
        // Redirect to useradmin page, set department to that of the deleted user
        return $this->redirectToRoute('useradmin_filter_users_by_department', array('id' => $user->getDepartment()->getId()));
    }

    public function sendActivationMailAction(User $user)
    {
        $this->get('app.user.registration')->sendActivationCode($user);

        return $this->redirectToRoute('useradmin_show');
    }
}
