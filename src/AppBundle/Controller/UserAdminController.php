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
            'department' => $department,
            'user_role' => $this->isGranted(Roles::TEAM_LEADER) ? Roles::TEAM_LEADER : Roles::TEAM_MEMBER,
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $roleAlias = $form->get('role')->getData();
            if (!$this->get('app.roles')->loggedInUserCanCreateUserWithRole($roleAlias)) {
                throw new BadRequestHttpException();
            }

            $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findByRoleName($this->get('app.roles')->mapAliasToRole($roleAlias));
            $user->addRole($role);

            // Persist the user
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
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $activeUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllInActiveUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $allDepartments,
            'department' => $department,
        ));
    }

    public function showUsersByDepartmentAction(Department $department)
    {
        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        $activeUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAllInActiveUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', array(
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $allDepartments,
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

    public function activateNewUserAction(Request $request, $newUserCode)
    {
        $user = $this->get('app.user.registration')->activateUserByNewUserCode($newUserCode);

        $form = $this->createForm(new NewUserType(), $user);

        $form->handleRequest($request);

        //Checks if the form is valid
        if ($form->isValid()) {
            //Updates the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //renders the login page, with a feedback message so that the user knows that the new password was stored.
            $feedback = 'Logg inn med din nye bruker';

            return $this->render('Login/login.html.twig', array('message' => $feedback, 'error' => null, 'last_username' => $user->getUserName()));
        }
        //Render reset_password twig with the form.
        return $this->render('new_user/create_new_user.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function sendActivationMailAction(User $user)
    {
        $this->get('app.user.registration')->sendActivationCode($user);

        return $this->redirectToRoute('useradmin_show');
    }
}
