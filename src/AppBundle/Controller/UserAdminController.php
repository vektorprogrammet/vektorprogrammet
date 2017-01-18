<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        ));
    }

    public function showAction()
    {
        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Finds the users for the given department
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', array(
            'users' => $users,
            'departments' => $allDepartments,
        ));
    }

    public function showUsersByDepartmentAction(Department $department)
    {
        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        // Finds the  users of the departmend with the departmed id of $id
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', array(
            'users' => $users,
            'departments' => $allDepartments,
        ));
    }

    public function deleteUserByIdAction(User $user)
    {
        // If Non-ROLE_HIGHEST_ADMIN try to delete user in other department
        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $this->getUser()->getDepartment) {
            throw new BadRequestHttpException();
        }
        try {
            // This deletes the given user
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
            ));
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det er ikke mulig å slette brukeren. Vennligst kontakt IT-ansvarlig.',
            ]);
        }
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
}
